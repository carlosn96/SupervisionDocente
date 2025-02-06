
$(document).ready(() => {
    
    enviarFormulario("#formVerificacionCodigo", "../registro/api/RegistroAPI.php", "verificar_codigo", function(res) {
        print(res);
    });
    
    const inputs = $('input[type="text"]');

    inputs.on('input', function () {
        const index = inputs.index(this);
        if (this.value.length === 1) {
            if (index < inputs.length - 1) {
                inputs.eq(index + 1).val('').focus();
            } else {
                if($(this).closest('form').validate()) {
                    $(this).closest('form').submit();
                }
            }
        }
    });

    inputs.on('keydown', function (e) {
        const index = inputs.index(this);
        if (e.key === 'Backspace') {
            if (this.value.length === 0 && index > 0) {
                inputs.eq(index - 1).focus().val('');
            }
        }
    });
});