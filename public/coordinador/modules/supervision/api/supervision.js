

let chart;
let rubroContablesContainer;
let rubroNoContablesContainer;

function ready() {
    $(document).ready(function () {
        recuperarInfoAgenda();
    });
}

function generarComentarios(model) {
    let data = "model=" + model;
    crearPeticion("api/SupervisionAPI.php", {case: "generar_comentarios_supervision", data: data}, (res) => {
        print(res);
        $("#conclusionesArea").val(res.retroalimentacion);
    }, "json");
}

function guardarSupervision() {
    let data = {
        rubros: {
            contable: rubroContablesContainer.getSupervision(),
            no_contable: rubroNoContablesContainer.getSupervision()
        },
        fecha: $("#fechaHoraSupervision").val(),
        tema: $("#temaClase").val(),
        conclusion_general: $("#conclusionesArea").val(),
        id_agenda: $("#id_agenda").val()
    };
    //print(data);
    crearPeticion("api/SupervisionAPI.php", {case: "guardar_supervision", data: $.param(data)});
}

function recuperarInfoAgenda() {
    let idAgenda = $("#id_agenda").val();
    const redirigir = function () {
        redireccionar("../agenda");
    };
    if (idAgenda) {
        crearPeticion("api/SupervisionAPI.php", {case: "obtener_info_agenda", data: "id_agenda=" + idAgenda}, function (res) {
            //print(res);
            if ((Array.isArray(res) && res.length === 0) || !(typeof res === 'object' && res !== null)) {
                redirigir();
            } else {
                $("#fechaHoraSupervision").val(getFechaHoraActual());
                const profesor = Object.keys(res)[0];
                const info = res[profesor];
                const correo = info.correo_electronico;
                const perfil = info.perfil_profesional;
                const materias = Object.keys(info.materias);
                const materia = materias[0];
                const horario = info.materias[materia].horarios[0];
                const diaSemana = horario.dia_semana;
                const horaInicio = horario.hora_inicio;
                const horaFin = horario.hora_fin;
                const esProfesorAgendado = info.es_profesor_agendado;
                const idAgenda = info.id_agenda;
                const fechaAgenda = info.fecha_agenda;
                const supervisionHecha = info.supervision_hecha ? 'Sí' : 'No';
                const cardContent = `
                <div class="mb-3 p-3 border rounded">
                    <div class="text-white p-2 rounded">
                        <h5 class="mb-0">${profesor}</h5>
                        <h6 class="text-muted">(${perfil})</h6>
                        <p> <strong> <a href="https://mail.google.com/mail/?view=cm&fs=1&to=${correo}" target="_blank">${correo}</a> </strong> </p>
                    </div>
                    <h6 class="mt-3">Materia:</h6>
        <ul class="list-group mb-3">
            <li class="list-group-item"><strong>${materia}</strong></li>
            <li class="list-group-item">
                <div class="d-flex justify-content-between">
                    <span class="badge bg-info">${diaSemana}</span>
                    <span>${horaInicio} - ${horaFin}</span>
                </div>
            </li>
        </ul>
        <h6 class="mt-3">Información de Agenda</h6>
        <ul class="list-group">
            <li class="list-group-item"><strong>Fecha de Agenda:</strong> ${fechaAgenda}</li>
            <li class="list-group-item"><strong>¿Supervisión Hecha?:</strong> ${supervisionHecha}</li>
        </ul>
                </div>
                `;
                $('#tituloInfoDocente').append(profesor);
                $('#profesor-card').html(cardContent);
                if (info.supervision_hecha) {
                    redireccionar("../supervision_preview?id_agenda=" + idAgenda);
                } else {
                    recuperarCriteriosSupervision();
                }
            }
        }, "json");
    } else {
        redirigir();
    }
}


function recuperarCriteriosSupervision() {
    crearPeticion("api/SupervisionAPI.php", {case: "recuperar_criterios_por_rubro"}, res => {
        let rs = JSON.parse(res);
        let contables = rs.contable;
        let noContables = rs.no_contable;
        if (contables.length > 0 && noContables.length > 0) {
            const $containerContables = $('#containerContables');
            const $btnsNavegacionContables = $("#btnsNavegacionContables");
            const $containerNoContables = $('#containerNoContables');
            const $btnsNavegacionNoContables = $("#btnsNavegacionNoContables");
            rubroContablesContainer = new RubrosContainer(contables, $containerContables, $btnsNavegacionContables, "contable", "primary", actualizarGrafica);
            rubroNoContablesContainer = new RubrosContainer(noContables, $containerNoContables, $btnsNavegacionNoContables, "no_contable", "info");
            crearGrafica();
            actualizarGrafica();
        } else {
            alert("Sin información disponible");
        }
    });
}

function abrirModalEnviarSupervision() {
    const modalBody = $('#modalEnviarSupervisionBodyContent');
    modalBody.empty();

    // Añade la fecha y hora
    modalBody.append($("<div>", {class: "mb-3",
        html: `<div class="input-group input-group-lg">
                    <span class="input-group-text">Fecha y hora</span>
                    <input readonly type="datetime-local" class="form-control" value="${$("#fechaHoraSupervision").val()}">
                </div>
            </div>`}));

    // Contenedor para la tabla de rubros contables con borde
    const contablesContainer = $('<div>', {class: 'border p-3 mb-3 rounded'});

    // Añade la puntuación por categoría
    $.each(rubroContablesContainer.getPuntuacionCategoria(), function (key, value) {
        let element = $('<div>', {class: 'mb-3'});
        element.html(`
            <div class="d-flex justify-content-between">
                <span><strong>${key}:</strong></span>
                <span>${value}%</span>
            </div>
        `);
        contablesContainer.append(element);
    });

    modalBody.append(contablesContainer);

    // Añade los rubros y criterios cumplidos de rubros no contables
    const supervision = rubroNoContablesContainer.getSupervision();
    const listGroup = $('<ol>', {class: 'list-group list-group-numbered mb-3'}); // Añade un margen inferior a la lista

    supervision.forEach(rubro => {
        const listItem = $('<li>', {class: 'list-group-item d-flex justify-content-between align-items-start'});
        const contentDiv = $('<div>', {class: 'ms-2 me-auto'});
        contentDiv.append($('<div>', {class: 'fw-bold', text: rubro.rubro}));
        const criteriosCumplidos = rubro.criterios.filter(criterio => criterio.cumplido);
        if (criteriosCumplidos.length > 0) {
            criteriosCumplidos.forEach(criterio => {
                contentDiv.append($('<div>', {text: criterio.label + " (" + criterio.comentario + ")"}));
            });
            listItem.append(contentDiv);
            listItem.append($('<span>', {class: 'badge text-bg-primary rounded-pill', text: criteriosCumplidos.length}));
        } else {
            contentDiv.append($('<div>', {text: 'Sin elementos anotados'}));
            listItem.append(contentDiv);
            listItem.append($('<span>', {class: 'badge text-bg-secondary rounded-pill', text: '0'}));
        }
        listGroup.append(listItem);
    });

    modalBody.append(listGroup);

    // Añade el tema de la clase con un margen superior
    modalBody.append($("<div>", {class: "row mb-3 mt-4",
        html: `
            <label for="temaClaseModal" class="col-sm-2 col-form-label col-form-label-sm">Tema</label>
            <div class="col-sm-10">
                <input readonly value="${$("#temaClase").val()}" class="form-control form-control-sm" id="temaClaseModal">
            </div>`
    }));

    // Añade los comentarios de la clase y ajusta su altura
    const conclusiones = $("#conclusionesArea").val();
    const conclusionesHeight = conclusiones.split('\n').length * 20; // Ajusta según la longitud del contenido
    modalBody.append($("<div>", {class: "form-floating",
        html: `
            <textarea readonly class="form-control" id="conclusionesClase" style="height: ${conclusionesHeight}px">${conclusiones}</textarea>
            <label for="conclusionesClase">Comentarios de clase</label>
        `}));

    $("#modalEnviarSupervision").modal("show");
}

function chartZoom() {
    // Initialize and show the modal
    const chartModal = new bootstrap.Modal(document.getElementById('chartModal'));
    chartModal.show();

    // Clone the chart's existing options
    const modalChartOptions = $.extend(true, {}, chart.w.config);  // Clone the current chart configuration

    // Render the chart in the modal
    const modalChart = new ApexCharts(document.querySelector("#chartInModal"), modalChartOptions);
    modalChart.render();

    // Clean up the chart in the modal when the modal is closed
    $('#chartModal').on('hidden.bs.modal', function () {
        modalChart.destroy();
    });
}

function crearGrafica() {
    var options = {
        series: [{
                name: 'Puntuación alcanzada',
                data: Object.values(rubroContablesContainer.puntuacionPorCategoria)
            }],
        chart: {
            height: 350,
            type: 'bar',
            zoom: {
                enabled: true  // Inicialmente el zoom está deshabilitado
            },
            toolbar: {
                show: true
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '70%',
                borderRadius: 10,
                dataLabels: {
                    position: 'top'  // top, center, bottom
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(2) + "%";
            },
            offsetY: -20,
            style: {
                fontSize: '12px',
                colors: ["#304758"]
            }
        },
        xaxis: {
            categories: Object.keys(rubroContablesContainer.puntuacionPorCategoria),
            labels: {
                style: {
                    fontSize: '14px'
                }
            }
        },
        yaxis: {
            title: {
                text: 'Puntuación (%)',
                style: {
                    fontSize: '16px'
                }
            },
            labels: {
                style: {
                    fontSize: '14px'
                }
            }
        },
        title: {
            text: 'Valoración Global',
            align: 'center',
            floating: true,
            offsetY: 0,
            style: {
                fontSize: '18px'
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val.toFixed(2) + "%";
                }
            }
        },
        legend: {
            show: true,
            position: 'top',
            horizontalAlign: 'center',
            floating: false,
            fontSize: '14px',
            labels: {
                useSeriesColors: true
            },
            markers: {
                width: 12,
                height: 12,
                strokeWidth: 0,
                fillColors: undefined,
                radius: 12,
                customHTML: undefined,
                onClick: undefined,
                offsetX: 0,
                offsetY: 0
            },
            itemMargin: {
                horizontal: 10,
                vertical: 5
            },
            onItemClick: {
                toggleDataSeries: true
            },
            onItemHover: {
                highlightDataSeries: true
            }
        }
    };
    chart = new ApexCharts(document.querySelector("#valoracionGlobal"), options);
    chart.render();
}

function actualizarGrafica() {
    let valores = Object.values(rubroContablesContainer.puntuacionPorCategoria);
    print(valores);
    chart.updateOptions({
        series: [{
                data: valores
            }],
        title: {
            text: 'Valoración Global (' + (valores.reduce((acc, currentValue) => acc + currentValue, 0) / valores.length).toFixed(2) + '%)'
        }
    });
}

class RubrosContainer {
    constructor(rubros, $container, $btnContainer, tipo, color, fnChange = () => {}) {
        this.rubros = rubros;
        this.$container = $container;
        this.currentStep = 0;
        this.totalSteps = rubros.length;
        this.tipo = tipo;
        this.color = color;
        this.fnChange = fnChange;
        this.puntuacionPorCategoria = {};
        if (this.totalSteps > 0) {
            this.#crearBotones($btnContainer);
            this.#renderRubros();
    }
    }

    #crearBotones($btnContainer) {
        this.$prevBtn = $('<button>', {class: "btn btn-outline-secondary"}).text("Anterior");
        this.$nextBtn = $('<button>', {class: "btn btn-outline-primary"}).text("Siguiente");
        this.$prevBtn.on('click', this.#accionAnterior.bind(this));
        this.$nextBtn.on('click', this.#accionSiguiente.bind(this));
        $btnContainer.append(this.$prevBtn);
        $btnContainer.append(this.$nextBtn);
    }

    #accionAnterior() {
        if (this.currentStep > 0) {
            this.currentStep--;
            this.#mostrarPaso(this.currentStep);
            this.$nextBtn.attr("disabled", false);
        }
        this.$prevBtn.attr("disabled", this.currentStep === 0);
    }

    #accionSiguiente() {
        this.$prevBtn.attr("disabled", false);
        if (this.currentStep < this.totalSteps - 1) {
            this.currentStep++;
            this.#mostrarPaso(this.currentStep);
            if (this.currentStep === this.totalSteps - 1) {
                this.$nextBtn.attr("disabled", true);
            }
        }
    }

    #mostrarPaso(step) {
        $(`.bs-stepper-content > .step-${this.tipo}-content`).hide();
        $(`#step-${this.tipo}-${step}`).show();
    }

    #renderRubros() {
        this.rubros.forEach((rubro, index) => {
            let descripcionRubro = rubro.descripcion.trim();
            this.puntuacionPorCategoria[descripcionRubro] = 0;
            const $stepContent = $('<div>', {id: `step-${this.tipo}-${index}`, role: 'tabpanel', 'aria-labelledby': `stepper-trigger-${index}`, name: `${rubro.id_rubro}`, class: `step-${this.tipo}-content`, value: rubro.descripcion});
            const $card = $('<div>', {class: 'card mb-4', id: descripcionRubro});
            const $cardHeader = $('<div>', {class: `card-header bg-${this.color} text-white d-flex justify-content-between align-items-center sticky-top`});
            const $rubroTitle = $('<span>', {text: descripcionRubro});
            const $rubroPercentage = this.tipo === "contable" ? $('<span>', {class: 'badge bg-light text-dark', text: '0%'}) : null;

            // Agregar el campo de búsqueda
            const $searchInput = $('<input>', {type: 'text', class: 'form-control bg-white form-control-sm', placeholder: 'Buscar criterio...', style: 'margin-left: 10px; width: 250px;'});
            $searchInput.on('input', function () {
                const searchTerm = $(this).val().toLowerCase();
                $tbody.find('tr').each(function () {
                    const criterioText = $(this).find('label').text().toLowerCase();
                    $(this).toggle(criterioText.includes(searchTerm));
                });
            });

            $cardHeader.append($rubroTitle).append($rubroPercentage).append($searchInput);
            $card.append($cardHeader);
            const $tableResponsive = $('<div>', {class: 'table-responsive'});
            const $table = $('<table>', {class: 'table table-striped'});
            const $thead = $('<thead>', {class: 'sticky-top bg-light'});
            const $theadRow = $('<tr>');
            $theadRow.append($('<th>', {text: 'Cumplido', class: 'w-auto'}));
            $theadRow.append($('<th>', {text: 'Criterio', class: 'w-50'}));
            $theadRow.append($('<th>', {text: 'Comentario', class: 'w-50'}));
            $thead.append($theadRow);
            $table.append($thead);
            const $tbody = $('<tbody>');
            rubro.criterios.forEach(criterio => {
                const $row = $('<tr>');
                const $formCheck = $('<div>', {class: 'form-check form-switch'});
                const $checkBox = $('<input>', {type: 'checkbox', class: 'form-check-input', id: "criterio" + criterio.id_criterio});
                $checkBox.on('change', () => {
                    this.#actualizarPorcentaje($card);
                    this.fnChange();
                });
                $formCheck.append($checkBox);
                const $tdCheckBox = $('<td>').append($formCheck);
                const $tdEnunciado = $('<td>').append($('<label>', {class: 'form-check-label mb-0', text: criterio.descripcion.trim(), for : "criterio" + criterio.id_criterio}));
                const $tdComentario = $('<td>').append($('<input>', {type: 'text', class: 'form-control', placeholder: 'Comentario'}));
                $row.append($tdCheckBox).append($tdEnunciado).append($tdComentario);
                $tbody.append($row);
            });
            $table.append($tbody);
            $tableResponsive.append($table);
            $card.append($tableResponsive);
            $stepContent.append($card);
            this.$container.append($stepContent);
        });
        $('[data-bs-toggle="tooltip"]').tooltip();
        this.#mostrarPaso(this.currentStep);
    }

    #actualizarPorcentaje($card) {
        if (this.tipo === "contable") {
            const $checkBoxes = $card.find('input[type="checkbox"]');
            const checkedCount = $checkBoxes.filter(':checked').length;
            const percentage = +((checkedCount / $checkBoxes.length) * 100).toFixed(2);
            $card.find('.badge').text(`${percentage}%`);
            this.puntuacionPorCategoria[$card.attr("id")] = percentage; // Comentado porque la variable no está definida en este código
        }
    }

    getPuntuacionCategoria() {
        return this.puntuacionPorCategoria;
    }

    getSupervision() {
        const supervision = [];
        // Recorre cada paso para recuperar los criterios seleccionados, los comentarios y el estado de cumplimiento
        this.$container.find('.step-' + this.tipo + '-content').each(function (index) {
            const $step = $(this);
            const $rows = $step.find('tbody tr');
            // Recopila la información de los criterios seleccionados, los comentarios y el estado de cumplimiento en este paso
            const criteriosSeleccionados = $rows.map(function () {
                const $checkBox = $(this).find('input[type="checkbox"]');
                const id = $checkBox.attr('id').replace('criterio', ''); // Extrae el ID del criterio
                const cumplido = $checkBox.is(':checked'); // Verifica si el criterio fue marcado como cumplido o no
                const comentario = $(this).find('input[type="text"]').val(); // Recupera el comentario del criterio
                const label = $('label.form-check-label[for="criterio' + id + '"]').html();
                return {
                    id: id,
                    cumplido: cumplido,
                    comentario: comentario,
                    label: label
                };
            }).get();
            // Agrega la información del paso actual al objeto de supervisión
            supervision.push({
                "id_rubro": $step.attr("name"),
                "rubro": $step.attr("value"),
                criterios: criteriosSeleccionados // Los criterios seleccionados en este paso
            });
        });
        return supervision;
    }

}

function actualizarGrafica() {
    let valores = Object.values(rubroContablesContainer.getPuntuacionCategoria());
    print(valores);
    chart.updateOptions({
        series: [{
                data: valores
            }],
        title: {
            text: 'Valoración Global (' + (valores.reduce((acc, currentValue) => acc + currentValue, 0) / valores.length).toFixed(2) + '%)'
        }
    });
}