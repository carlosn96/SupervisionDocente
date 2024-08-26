-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-08-2024 a las 01:56:46
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `supervision_docente`
--

DELIMITER $$
--
-- Procedimientos
--
DROP PROCEDURE IF EXISTS `actualizar_config_usuario`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_config_usuario` (IN `p_id_usuario` INT, IN `p_api_key_gpt` VARCHAR(100), IN `p_api_key_huggingface` VARCHAR(100), IN `p_id_plantel_actual` INT, IN `p_id_carrera_actual` INT)   BEGIN
    DECLARE cnt INT;

    -- Verificar si la fila existe
    SELECT COUNT(*) INTO cnt FROM config_usuario WHERE id_usuario = p_id_usuario;
    -- Si la fila no existe, insertar
    IF cnt = 0 THEN
        INSERT INTO config_usuario (id_usuario, api_key_gpt, api_key_huggingface, id_plantel_actual, id_carrera_actual) 
        VALUES (p_id_usuario, p_api_key_gpt, p_api_key_huggingface, p_id_plantel_actual, p_id_carrera_actual);
    END IF;
        -- Si la fila existe, actualizar
   UPDATE config_usuario SET
   api_key_gpt = p_api_key_gpt,
   api_key_huggingface = p_api_key_huggingface,
   id_plantel_actual = p_id_plantel_actual,
   id_carrera_actual = p_id_carrera_actual
   WHERE id_usuario = p_id_usuario;
END$$

DROP PROCEDURE IF EXISTS `actualizar_docente`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_docente` (IN `p_id_docente` INT(11), IN `p_nombre` VARCHAR(45), IN `p_apellidos` VARCHAR(45), IN `p_correo_electronico` VARCHAR(150), IN `p_perfil_profesional` VARCHAR(45))   BEGIN
    UPDATE docente 
    SET 
        nombre = p_nombre,
        apellidos = p_apellidos,
        correo_electronico = p_correo_electronico,
        perfil_profesional = p_perfil_profesional
    WHERE id_docente = p_id_docente;
END$$

DROP PROCEDURE IF EXISTS `agendar_supervision`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `agendar_supervision` (IN `id_horario_nuevo` INT, IN `fecha_nueva` DATE)   BEGIN
    DECLARE horario_conflicto INT;
    DECLARE coordinador_nuevo INT;

    -- Obtener el coordinador del nuevo horario
    SELECT cc.id_coordinador INTO coordinador_nuevo
    FROM materia_horarios mh
    JOIN materia m ON mh.id_materia = m.id_materia
    JOIN carrera_coordinador cc ON m.id_carrera = cc.id_carrera
    WHERE mh.id_horario = id_horario_nuevo;
    
    -- Verificar conflicto de horarios con el mismo coordinador
    SELECT 1 INTO horario_conflicto
    FROM materia_horarios mh
    JOIN materia m ON mh.id_materia = m.id_materia
    JOIN carrera_coordinador cc ON m.id_carrera = cc.id_carrera
    JOIN supervision_agenda sa ON mh.id_horario = sa.id_horario
    WHERE cc.id_coordinador = coordinador_nuevo
    AND mh.dia_semana = (SELECT dia_semana FROM materia_horarios WHERE id_horario = id_horario_nuevo)
    AND (
        (mh.hora_inicio < (SELECT hora_fin FROM materia_horarios WHERE id_horario = id_horario_nuevo)
         AND mh.hora_fin > (SELECT hora_inicio FROM materia_horarios WHERE id_horario = id_horario_nuevo))
    )
    AND sa.fecha = fecha_nueva;
    
    IF horario_conflicto IS NULL THEN
        -- No hay conflicto, se puede insertar
        INSERT INTO supervision_agenda (id_horario, fecha) VALUES (id_horario_nuevo, fecha_nueva);
    ELSE
        -- Hay un conflicto
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Conflicto de horario detectado con otro curso del mismo coordinador.';
    END IF;
END$$

DROP PROCEDURE IF EXISTS `insertar_coordinador`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertar_coordinador` (IN `p_tipo_usuario` ENUM('Coordinador','Administrador'), IN `p_nombre` VARCHAR(60), IN `p_apellidos` VARCHAR(60), IN `p_correo_electronico` VARCHAR(50), IN `p_contrasenia` VARCHAR(500), IN `p_avatar` LONGTEXT, IN `p_carreras_coordina` JSON)   BEGIN
    DECLARE p_id_usuario INT;
    DECLARE p_id_coordinador INT;
    DECLARE p_id_carrera INT;
    DECLARE i INT DEFAULT 0;
    DECLARE total_carreras_coordina INT;

    -- Insertar el nuevo usuario en la tabla usuario
    INSERT INTO usuario (tipo_usuario, nombre, apellidos, correo_electronico, contrasenia, avatar)
    VALUES (p_tipo_usuario, p_nombre, p_apellidos, p_correo_electronico, p_contrasenia, p_avatar);

    -- Obtener el ID del usuario recién insertado
    SET p_id_usuario = LAST_INSERT_ID();

    -- Insertar el coordinador en la tabla coordinador usando el ID del usuario
    INSERT INTO coordinador (id_usuario) VALUES (p_id_usuario);
    
    SET p_id_coordinador = LAST_INSERT_ID();
    SET total_carreras_coordina = JSON_LENGTH(p_carreras_coordina);
    
    WHILE i < total_carreras_coordina DO
             SET p_id_carrera = JSON_UNQUOTE(JSON_EXTRACT(p_carreras_coordina, CONCAT('$[', i, ']')));
             INSERT INTO carrera_coordinador (id_carrera, id_coordinador) VALUES (p_id_carrera, p_id_coordinador);
            SET i = i + 1;
	END WHILE;
END$$

DROP PROCEDURE IF EXISTS `insertar_docente_materias_horarios`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertar_docente_materias_horarios` (IN `p_nombre` VARCHAR(45), IN `p_apellidos` VARCHAR(45), IN `p_correo_electronico` VARCHAR(150), IN `p_perfil_profesional` VARCHAR(45), IN `p_id_coordinador` INT, IN `p_id_carrera` INT, IN `p_id_plantel` INT, IN `p_materias` JSON)   BEGIN

    DECLARE last_docente_id INT;

    DECLARE last_materia_id INT;

    DECLARE i INT DEFAULT 0;

    DECLARE j INT DEFAULT 0;

    DECLARE materias_length INT;

    DECLARE horarios_length INT;

    DECLARE nombre_materia VARCHAR(255);
    DECLARE grupo_materia VARCHAR(45);

    DECLARE id_carrera INT;

    DECLARE dia_semana ENUM('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');

    DECLARE hora_inicio TIME;

    DECLARE hora_fin TIME;



    -- Insertar el docente

    INSERT INTO docente (nombre, apellidos, correo_electronico, perfil_profesional, id_coordinador)

    VALUES (p_nombre, p_apellidos, p_correo_electronico, p_perfil_profesional, p_id_coordinador);

    

    -- Obtener el ID del docente recién insertado

    SET last_docente_id = LAST_INSERT_ID();



    -- Obtener la longitud del array de materias

    SET materias_length = JSON_LENGTH(p_materias);



    -- Loop para insertar cada materia

    WHILE i < materias_length DO

        SET nombre_materia = JSON_UNQUOTE(JSON_EXTRACT(p_materias, CONCAT('$[', i, '].nombre')));
        SET grupo_materia = JSON_UNQUOTE(JSON_EXTRACT(p_materias, CONCAT('$[', i, '].grupo')));
        

        -- Insertar la materia

        INSERT INTO materia (nombre, grupo, id_docente, id_carrera, id_plantel)

        VALUES (nombre_materia, grupo_materia, last_docente_id, p_id_carrera, p_id_plantel);



        -- Obtener el ID de la materia recién insertada

        SET last_materia_id = LAST_INSERT_ID();



        -- Obtener la longitud del array de horarios

        SET horarios_length = JSON_LENGTH(JSON_EXTRACT(p_materias, CONCAT('$[', i, '].horario')));



        -- Loop para insertar cada horario

        WHILE j < horarios_length DO

            SET dia_semana = JSON_UNQUOTE(JSON_EXTRACT(p_materias, CONCAT('$[', i, '].horario[', j, '].dia_semana')));

            SET hora_inicio = JSON_UNQUOTE(JSON_EXTRACT(p_materias, CONCAT('$[', i, '].horario[', j, '].hora_inicio')));

            SET hora_fin = JSON_UNQUOTE(JSON_EXTRACT(p_materias, CONCAT('$[', i, '].horario[', j, '].hora_fin')));



            -- Insertar el horario

            INSERT INTO materia_horarios (id_materia, dia_semana, hora_inicio, hora_fin)

            VALUES (last_materia_id, dia_semana, hora_inicio, hora_fin);



            SET j = j + 1;

        END WHILE;



        -- Reset the inner loop counter

        SET j = 0;



        SET i = i + 1;

    END WHILE;

END$$

DROP PROCEDURE IF EXISTS `insertar_supervision`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertar_supervision` (IN `p_id_agenda` INT, IN `p_fecha` DATETIME, IN `p_tema` VARCHAR(100), IN `p_conclusion_general` TEXT, IN `p_criterios_contables` JSON, IN `p_criterios_no_contables` JSON)   BEGIN

    DECLARE id_supervision_realizada INT;

    DECLARE contador_rubros INT;

    DECLARE contador_criterios INT;

    DECLARE id_criterio INT;

    DECLARE cumplido TINYINT;

    DECLARE comentario_text VARCHAR(100); -- Cambiar el nombre de la variable para evitar conflictos

    DECLARE i INT DEFAULT 0;

    DECLARE j INT DEFAULT 0;



    -- Insertar en supervision_realizada

    INSERT INTO supervision_realizada (id_agenda, fecha, tema, conclusion_general)

    VALUES (p_id_agenda, p_fecha, p_tema, p_conclusion_general);



    -- Obtener el id de la última supervisión insertada

    SET id_supervision_realizada = LAST_INSERT_ID();

    

	SET contador_rubros = JSON_LENGTH(p_criterios_contables);

    

    WHILE i < contador_rubros DO

        SET contador_criterios = JSON_LENGTH(JSON_EXTRACT(p_criterios_contables, CONCAT('$[', i, '].criterios')));

        WHILE j < contador_criterios DO

            SET id_criterio = JSON_UNQUOTE(JSON_EXTRACT(p_criterios_contables, CONCAT('$[', i, '].criterios[', j, '].id_criterio')));

            SET cumplido = IF(JSON_UNQUOTE(JSON_EXTRACT(p_criterios_contables, CONCAT('$[', i, '].criterios[', j, '].cumplido'))) = 'true', 1, 0);

            SET comentario_text = LEFT(JSON_UNQUOTE(JSON_EXTRACT(p_criterios_contables, CONCAT('$[', i, '].criterios[', j, '].comentario'))), 100);

            

            -- Usar parámetros en la consulta y asegurar que el comentario esté limpio de caracteres especiales

            INSERT INTO supervision_realizada_contable_detalles (id_supervision, id_criterio, criterio_cumplido, comentario) 

            VALUES (id_supervision_realizada, id_criterio, cumplido , comentario_text);

            

            SET j = j + 1;

        END WHILE;

        SET i = i + 1;

        SET j = 0;

    END WHILE;

    

    SET contador_rubros = JSON_LENGTH(p_criterios_no_contables);

    SET i = 0;

    WHILE i < contador_rubros DO

        SET contador_criterios = JSON_LENGTH(JSON_EXTRACT(p_criterios_no_contables, CONCAT('$[', i, '].criterios')));

        WHILE j < contador_criterios DO

            SET id_criterio = JSON_UNQUOTE(JSON_EXTRACT(p_criterios_no_contables, CONCAT('$[', i, '].criterios[', j, '].id_criterio')));

            SET cumplido = IF(JSON_UNQUOTE(JSON_EXTRACT(p_criterios_no_contables, CONCAT('$[', i, '].criterios[', j, '].cumplido'))) = 'true', 1, 0);

            SET comentario_text = LEFT(JSON_UNQUOTE(JSON_EXTRACT(p_criterios_no_contables, CONCAT('$[', i, '].criterios[', j, '].comentario'))), 100);

            

            -- Usar parámetros en la consulta y asegurar que el comentario esté limpio de caracteres especiales

            INSERT INTO supervision_realizada_no_contable_detalles (id_supervision, id_criterio, criterio_cumplido, comentario) 

            VALUES (id_supervision_realizada, id_criterio, cumplido , comentario_text);

            

            SET j = j + 1;

        END WHILE;

        SET i = i + 1;

        SET j = 0;

    END WHILE;



    -- Actualizar la tabla supervision_agenda

    UPDATE supervision_agenda

    SET supervision_hecha = 1

    WHERE id_agenda = p_id_agenda;

END$$

DROP PROCEDURE IF EXISTS `obtener_supervision_agenda`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `obtener_supervision_agenda` (IN `p_fecha` DATE, IN `p_id_coordinador` INT)   BEGIN

    SELECT 

        CONCAT(d.nombre, ' ', d.apellidos) AS nombre_docente,

        DATE_FORMAT(sa.fecha, '%d-%m-%Y') AS fecha,

        mh.dia_semana,

        mh.hora_inicio,

        mh.hora_fin,

        m.nombre AS nombre_materia

    FROM 

        supervision_agenda sa

    JOIN 

        materia_horarios mh ON sa.id_horario = mh.id_horario

    JOIN 

        materia m ON mh.id_materia = m.id_materia

    JOIN 

        docente d ON m.id_docente = d.id_docente

    WHERE 

        MONTH(sa.fecha) = MONTH(p_fecha) AND

        YEAR(sa.fecha) = YEAR(p_fecha) AND

        d.id_coordinador = p_id_coordinador

    ORDER BY 

        sa.fecha;

END$$

DROP PROCEDURE IF EXISTS `recuperar_agenda_por_fecha`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `recuperar_agenda_por_fecha` (IN `p_fecha` DATE, IN `p_id_coordinador` INT, IN `p_id_carrera` INT, IN `p_id_plantel` INT)   BEGIN

    SELECT 

    *

    FROM 

        listar_agenda_supervision a

    WHERE 

        MONTH(a.fecha) = MONTH(p_fecha) AND

        YEAR(a.fecha) = YEAR(p_fecha) AND

        a.id_coordinador = p_id_coordinador AND

        a.id_carrera = p_id_carrera AND

        a.id_plantel = p_id_plantel

    ORDER BY 

        a.fecha, a.hora_inicio;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

DROP TABLE IF EXISTS `administrador`;
CREATE TABLE `administrador` (
  `id_administrador` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrera`
--

DROP TABLE IF EXISTS `carrera`;
CREATE TABLE `carrera` (
  `id_carrera` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `tipo` enum('Lic.','Ing.') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carrera`
--

INSERT INTO `carrera` (`id_carrera`, `nombre`, `tipo`) VALUES
(35, 'Derecho', 'Lic.'),
(36, 'Contaduría Pública', 'Lic.'),
(38, 'Cultura Física y Deportes', 'Lic.'),
(39, 'Civil', 'Ing.'),
(40, 'Filosofía', 'Lic.'),
(41, 'Criminalística', 'Lic.'),
(52, 'Abogado', 'Lic.'),
(53, 'Psicología', 'Lic.'),
(56, 'Computación', 'Ing.'),
(57, 'Comunicaciones y Electrónica', 'Ing.'),
(58, 'Ciencias de la Comunicación', 'Lic.'),
(59, 'Diseño Gráfico', 'Lic.'),
(60, 'Administración', 'Lic.'),
(62, 'Industrial', 'Ing.'),
(65, 'Química', 'Lic.'),
(66, 'Químico Farmacéutico Biólogo', 'Lic.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrera_coordinador`
--

DROP TABLE IF EXISTS `carrera_coordinador`;
CREATE TABLE `carrera_coordinador` (
  `id_carrera` int(11) NOT NULL,
  `id_coordinador` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carrera_coordinador`
--

INSERT INTO `carrera_coordinador` (`id_carrera`, `id_coordinador`) VALUES
(56, 27),
(57, 27),
(65, 31),
(66, 31);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrera_plantel`
--

DROP TABLE IF EXISTS `carrera_plantel`;
CREATE TABLE `carrera_plantel` (
  `id_carrera` int(11) NOT NULL,
  `id_plantel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carrera_plantel`
--

INSERT INTO `carrera_plantel` (`id_carrera`, `id_plantel`) VALUES
(35, 11),
(35, 14),
(35, 16),
(35, 17),
(36, 13),
(36, 14),
(38, 13),
(38, 15),
(38, 17),
(39, 13),
(39, 14),
(39, 15),
(40, 11),
(40, 13),
(40, 15),
(40, 16),
(40, 17),
(41, 12),
(41, 13),
(41, 16),
(41, 17),
(52, 11),
(52, 13),
(52, 14),
(52, 16),
(52, 17),
(53, 12),
(53, 13),
(53, 14),
(53, 17),
(56, 11),
(57, 11),
(57, 14),
(57, 15),
(57, 16),
(58, 11),
(58, 14),
(59, 11),
(60, 11),
(60, 12),
(60, 15),
(60, 16),
(62, 11),
(62, 14),
(65, 15),
(65, 16),
(65, 20),
(66, 14),
(66, 15),
(66, 16),
(66, 20),
(66, 22),
(66, 23);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_usuario`
--

DROP TABLE IF EXISTS `config_usuario`;
CREATE TABLE `config_usuario` (
  `id_config` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `api_key_gpt` varchar(100) DEFAULT NULL,
  `api_key_huggingface` varchar(100) DEFAULT NULL,
  `id_plantel_actual` int(11) DEFAULT NULL,
  `id_carrera_actual` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `config_usuario`
--

INSERT INTO `config_usuario` (`id_config`, `id_usuario`, `api_key_gpt`, `api_key_huggingface`, `id_plantel_actual`, `id_carrera_actual`) VALUES
(4, 60, NULL, NULL, 11, 56),
(7, 64, NULL, NULL, 20, 65);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `consultar_detalles_supervision_realizada`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `consultar_detalles_supervision_realizada`;
CREATE TABLE `consultar_detalles_supervision_realizada` (
`id_supervision` int(11)
,`id_agenda` int(11)
,`id_horario` int(11)
,`fecha` datetime
,`tipo_criterio` varchar(11)
,`id_rubro` int(11)
,`rubro_descripcion` varchar(200)
,`id_criterio` int(11)
,`criterio_descripcion` varchar(200)
,`criterio_cumplido` tinyint(4)
,`comentario` varchar(100)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `consultar_supervision`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `consultar_supervision`;
CREATE TABLE `consultar_supervision` (
`id_supervision` int(11)
,`fecha_supervision` datetime
,`tema` varchar(100)
,`conclusion_general` text
,`id_agenda` int(11)
,`fecha_agenda` date
,`supervision_hecha` tinyint(4)
,`id_horario` int(11)
,`dia_semana` enum('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado')
,`hora_inicio` time
,`hora_fin` time
,`id_materia` int(11)
,`nombre_materia` varchar(255)
,`id_docente` int(11)
,`nombre_docente` varchar(45)
,`apellido_docente` varchar(45)
,`id_carrera` int(11)
,`nombre_carrera` varchar(263)
,`id_plantel` int(11)
,`nombre_plantel` varchar(255)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coordinador`
--

DROP TABLE IF EXISTS `coordinador`;
CREATE TABLE `coordinador` (
  `id_coordinador` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_administrador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `coordinador`
--

INSERT INTO `coordinador` (`id_coordinador`, `id_usuario`, `id_administrador`) VALUES
(27, 60, NULL),
(31, 64, NULL);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `coordinador_usuario`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `coordinador_usuario`;
CREATE TABLE `coordinador_usuario` (
`id_usuario` int(11)
,`id_coordinador` int(11)
,`nombre` varchar(60)
,`apellidos` varchar(60)
,`correo_electronico` varchar(50)
,`avatar` longtext
,`carreras_coordina` mediumtext
,`fecha_nacimiento` date
,`genero` enum('Masculino','Femenino')
,`telefono` varchar(20)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docente`
--

DROP TABLE IF EXISTS `docente`;
CREATE TABLE `docente` (
  `id_docente` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `apellidos` varchar(45) NOT NULL,
  `correo_electronico` varchar(150) NOT NULL,
  `perfil_profesional` varchar(45) NOT NULL,
  `id_coordinador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `docente`
--

INSERT INTO `docente` (`id_docente`, `nombre`, `apellidos`, `correo_electronico`, `perfil_profesional`, `id_coordinador`) VALUES
(63, 'José Ivan', 'Reyes Jimenez', 'jose.reyes@universidad-une.com', 'Ing. Sistemas Computacionales', 27),
(64, 'Clara', 'Bernal López', 'clara.bernal@universidad-une.com', 'Lic. Administración de Empresas', 27),
(75, 'Marie', 'Curie', 'marie.curie@universidad-une.com', 'Dr. Física', 31),
(76, 'Dimitri', 'Mendeléyev', 'dimitri.mendeleyev@universidad-une.com', 'Dr. Ciencias', 31),
(77, 'Linus', 'Pauling', 'linus.pauling@universidad-une.com', 'Dr. Química', 31),
(78, 'Josué', 'Larios García', 'josue.larios@universidad-une.com', 'Ing. Electrónica', 27),
(83, 'Susana', 'Huerta Varela', 'susana.huerta@universidad-une.com', 'Lic. Matemáticas', 27),
(84, 'José Alfredo', 'Mercado Aguirre', 'alfredo.mercado@universidad-une.com', 'Ing. Computación', 27),
(85, 'Juan Antonio', 'Martínez Carbajal', 'juanantonio.martinez@universidad-une.com', 'Ing. Computación', 27),
(86, 'Damián', 'Castellanos Flores', 'damian.castellanos@universidad-une.com', 'Lic. Matemáticas', 27),
(87, 'Jorge Abraham', 'Ponce Aguayo', 'jorgeabraham.ponce@universidad-une.com', 'Ing. Mecatrónica', 27),
(88, 'Citlalli Anahí', 'Salcedo Navarro', 'citlalli.salcedo@universidad-une.com', 'Lic. Administración', 27),
(89, 'Samuel', 'Díaz Amézquita', 'samuel.diaz@universidad-une.com', 'Lic. Diseño Industrial', 27),
(90, 'María Dolores', 'García Yerena', 'mariadolores.garcia@universidad-une.com', 'Lic.', 27),
(91, 'Salvador', 'Magaña Sánchez', 'salvador.magana@universidad-une.com', 'Mtro. Computación', 27),
(92, 'Jonathan Rogelio', 'Salas Gámez', 'jonathan.salas@universidad-une.com', 'Mtro. Computación', 27),
(94, 'Silvia Jazmin', 'Castellanos Ornelas', 'silvia.castellanos@universidad-une.com', 'Ing. Computación', 27);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `listar_agenda_supervision`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `listar_agenda_supervision`;
CREATE TABLE `listar_agenda_supervision` (
`id_coordinador` int(11)
,`id_agenda` int(11)
,`nombre_docente` varchar(91)
,`fecha` date
,`dia_semana` enum('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado')
,`hora_inicio` time
,`hora_fin` time
,`nombre_materia` varchar(255)
,`grupo_materia` varchar(45)
,`nombre_coordinador` varchar(121)
,`status` varchar(14)
,`id_carrera` int(11)
,`carrera` varchar(263)
,`id_plantel` int(11)
,`plantel` varchar(255)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `listar_carrera_detalles`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `listar_carrera_detalles`;
CREATE TABLE `listar_carrera_detalles` (
`id_carrera` int(11)
,`tipo` enum('Lic.','Ing.')
,`nombre` varchar(255)
,`coordinador_nombre` varchar(60)
,`coordinador_correo` varchar(50)
,`id_coordinador` varchar(11)
,`id_usuario_coordinador` varchar(11)
,`planteles` mediumtext
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `listar_criterios_supervision_por_rubro`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `listar_criterios_supervision_por_rubro`;
CREATE TABLE `listar_criterios_supervision_por_rubro` (
`id_rubro` int(11)
,`rubro_descripcion` varchar(200)
,`id_criterio` int(11)
,`descripcion_criterio` varchar(200)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `listar_criterios_supervision_por_rubro_no_contable`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `listar_criterios_supervision_por_rubro_no_contable`;
CREATE TABLE `listar_criterios_supervision_por_rubro_no_contable` (
`id_rubro` int(11)
,`rubro_descripcion` varchar(100)
,`id_criterio` int(11)
,`descripcion_criterio` varchar(100)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `listar_docente_materias_horarios`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `listar_docente_materias_horarios`;
CREATE TABLE `listar_docente_materias_horarios` (
`id_coordinador` int(11)
,`es_profesor_agendado` int(1)
,`id_agenda` int(11)
,`supervision_hecha` int(1)
,`id_docente` int(11)
,`nombre_docente` varchar(45)
,`apellido_docente` varchar(45)
,`correo_electronico` varchar(150)
,`perfil_profesional` varchar(45)
,`id_carrera` int(11)
,`id_plantel` int(11)
,`nombre_plantel` varchar(255)
,`nombre_materia` varchar(255)
,`grupo_materia` varchar(45)
,`id_materia` int(11)
,`id_horario` int(11)
,`dia_semana` enum('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado')
,`hora_inicio` time
,`hora_fin` time
,`total_horas` decimal(43,0)
,`es_horario_agendado` int(1)
,`fecha` date
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `lista_carreras_sin_coordinador`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `lista_carreras_sin_coordinador`;
CREATE TABLE `lista_carreras_sin_coordinador` (
`id_carrera` int(11)
,`nombre` varchar(260)
,`tipo` enum('Lic.','Ing.')
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materia`
--

DROP TABLE IF EXISTS `materia`;
CREATE TABLE `materia` (
  `id_materia` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `grupo` varchar(45) NOT NULL,
  `id_docente` int(11) NOT NULL,
  `id_carrera` int(11) NOT NULL,
  `id_plantel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materia`
--

INSERT INTO `materia` (`id_materia`, `nombre`, `grupo`, `id_docente`, `id_carrera`, `id_plantel`) VALUES
(35, 'Radio, Microondas y Satélites', '', 63, 57, 15),
(36, 'Dispositivos Lógicos Programables', '', 63, 57, 15),
(37, 'Ingeniería Económica', '', 64, 57, 15),
(50, 'Física Nuclear', '', 75, 65, 15),
(51, 'Química Inorgánica', '', 75, 65, 15),
(52, 'Radiología Médica', '', 75, 65, 15),
(53, 'Química General', '', 76, 65, 15),
(54, 'Historia de la Química', '', 76, 65, 15),
(55, 'Química Cuántica', '', 77, 65, 20),
(56, 'Biología Molecular', '', 77, 65, 20),
(57, 'Sistemas Operativos en Tiempo Real', '', 78, 57, 15),
(65, 'Ecuaciones Diferenciales', '3° ICOM', 83, 56, 11),
(66, 'Programación Orientada a Objetos', '3° ICOM', 84, 56, 11),
(67, 'Bases de Datos', '5° ICOM', 84, 56, 11),
(68, 'Redes de Computadoras', '5° ICOM', 84, 56, 11),
(69, 'Sistemas Digitales', '3° ICOM', 85, 56, 11),
(70, 'Simulación por Computadora', '7° INCO', 85, 56, 11),
(71, 'Álgebra Lineal', '3° ICOM', 86, 56, 11),
(72, 'Circuitos Electrónicos y Electromagnetismo', '3° ICOM', 87, 56, 11),
(73, 'Administración', '3° ICOM', 88, 56, 11),
(74, 'Seminario de Integración: Protocolo', '5° ICOM', 89, 56, 11),
(75, 'Formación Integral: Taller de Oratoria', '5° ICOM', 90, 56, 11),
(76, 'Análisis de Algoritmos', '5° ICOM', 91, 56, 11),
(77, 'Programación WEB', '6° INCO', 91, 56, 11),
(78, 'Sistemas Operativos 22B', '5° ICOM', 92, 56, 11),
(79, 'Sistemas Operativos 17B', '6° INCO', 92, 56, 11),
(80, 'Seminario de Solución de Problemas de Sistemas Operativos', '6° INCO', 92, 56, 11),
(81, 'Programación Dispositivos Móviles', '7° INCO', 92, 56, 11),
(83, 'Fundamentos de Inteligencia Artificial', '5° ICOM', 94, 56, 11),
(84, 'Traductores de Lenguajes II', '6° INCO', 94, 56, 11),
(85, 'Seminario de Solución de Problemas de Traductores de Lenguajes II', '6° INCO', 94, 56, 11),
(86, 'Inteligencia Artificial I', '7° INCO', 94, 56, 11),
(87, 'Seminario de Solución de Problemas de Inteligencia Artificial I', '7° INCO', 94, 56, 11),
(88, 'Desarrollo del Prototipo del Proyecto del Módulo II', '6° INCO', 94, 56, 11),
(89, 'Estado del Arte del Proyecto del Módulo III', '6° INCO', 94, 56, 11),
(90, 'Computación Tolerante a Fallas', '6° INCO', 84, 56, 11),
(91, 'Sistemas Operativos de Red', '7° INCO', 84, 56, 11),
(92, 'Seminario de Solución de Problemas de Sistemas Operativos de Red', '7° INCO', 84, 56, 11),
(93, 'Documentación y Defensa del Proyecto del Módulo II', '7° INCO', 92, 56, 11),
(94, 'Desarrollo del Prototipo del Proyecto del Módulo III', '7° INCO', 92, 56, 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materia_horarios`
--

DROP TABLE IF EXISTS `materia_horarios`;
CREATE TABLE `materia_horarios` (
  `id_horario` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `dia_semana` enum('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materia_horarios`
--

INSERT INTO `materia_horarios` (`id_horario`, `id_materia`, `dia_semana`, `hora_inicio`, `hora_fin`) VALUES
(61, 35, 'Lunes', '14:00:00', '15:00:00'),
(62, 35, 'Miércoles', '07:00:00', '10:00:00'),
(63, 36, 'Martes', '08:00:00', '11:00:00'),
(64, 37, 'Lunes', '11:00:00', '14:00:00'),
(65, 37, 'Martes', '11:00:00', '12:00:00'),
(82, 50, 'Lunes', '07:00:00', '09:00:00'),
(83, 50, 'Miércoles', '07:00:00', '09:00:00'),
(84, 51, 'Martes', '15:00:00', '17:00:00'),
(85, 51, 'Miércoles', '15:00:00', '16:00:00'),
(86, 52, 'Lunes', '19:00:00', '21:00:00'),
(87, 52, 'Martes', '18:00:00', '20:00:00'),
(88, 53, 'Lunes', '16:00:00', '19:00:00'),
(89, 53, 'Viernes', '18:00:00', '20:00:00'),
(90, 54, 'Miércoles', '17:00:00', '18:00:00'),
(91, 54, 'Viernes', '20:00:00', '21:00:00'),
(92, 55, 'Lunes', '14:00:00', '16:00:00'),
(93, 55, 'Miércoles', '15:30:00', '17:30:00'),
(94, 56, 'Jueves', '09:00:00', '11:00:00'),
(95, 57, 'Jueves', '09:00:00', '11:00:00'),
(96, 57, 'Viernes', '12:00:00', '13:00:00'),
(114, 65, 'Lunes', '10:30:00', '12:30:00'),
(116, 65, 'Martes', '10:30:00', '12:30:00'),
(117, 66, 'Lunes', '13:30:00', '14:30:00'),
(118, 66, 'Miércoles', '09:00:00', '10:00:00'),
(119, 66, 'Viernes', '11:30:00', '13:30:00'),
(120, 67, 'Lunes', '12:30:00', '13:30:00'),
(121, 67, 'Jueves', '08:00:00', '10:00:00'),
(122, 67, 'Viernes', '09:00:00', '11:30:00'),
(123, 68, 'Martes', '13:30:00', '14:30:00'),
(124, 68, 'Miércoles', '12:30:00', '14:30:00'),
(125, 69, 'Lunes', '12:30:00', '13:30:00'),
(126, 69, 'Miércoles', '13:30:00', '14:30:00'),
(127, 70, 'Lunes', '13:30:00', '14:30:00'),
(128, 70, 'Miércoles', '07:00:00', '10:00:00'),
(129, 71, 'Lunes', '08:00:00', '09:00:00'),
(130, 71, 'Miércoles', '12:30:00', '13:30:00'),
(131, 71, 'Jueves', '11:30:00', '13:30:00'),
(132, 72, 'Martes', '09:00:00', '10:00:00'),
(133, 72, 'Miércoles', '10:30:00', '12:30:00'),
(134, 72, 'Jueves', '08:00:00', '09:00:00'),
(135, 73, 'Jueves', '09:00:00', '11:30:00'),
(136, 73, 'Viernes', '09:00:00', '11:30:00'),
(137, 74, 'Lunes', '11:30:00', '12:30:00'),
(138, 74, 'Viernes', '11:30:00', '13:30:00'),
(139, 75, 'Lunes', '07:00:00', '11:30:00'),
(140, 76, 'Miércoles', '10:30:00', '12:30:00'),
(141, 76, 'Miércoles', '10:30:00', '12:30:00'),
(142, 77, 'Martes', '08:00:00', '10:00:00'),
(143, 77, 'Miércoles', '08:00:00', '10:00:00'),
(144, 78, 'Miércoles', '08:00:00', '10:00:00'),
(146, 78, 'Jueves', '10:30:00', '12:30:00'),
(147, 79, 'Lunes', '10:30:00', '11:30:00'),
(148, 79, 'Jueves', '07:00:00', '08:00:00'),
(149, 79, 'Viernes', '09:00:00', '11:30:00'),
(150, 80, 'Jueves', '08:00:00', '10:00:00'),
(151, 80, 'Miércoles', '10:30:00', '12:30:00'),
(152, 81, 'Jueves', '12:30:00', '13:30:00'),
(153, 81, 'Viernes', '11:30:00', '14:30:00'),
(155, 69, 'Jueves', '13:30:00', '14:30:00'),
(156, 69, 'Viernes', '13:30:00', '14:30:00'),
(157, 68, 'Viernes', '13:30:00', '14:30:00'),
(158, 83, 'Martes', '08:00:00', '10:00:00'),
(159, 83, 'Jueves', '12:30:00', '14:30:00'),
(160, 84, 'Lunes', '12:30:00', '14:30:00'),
(161, 84, 'Martes', '12:30:00', '14:30:00'),
(162, 85, 'Martes', '10:30:00', '12:30:00'),
(163, 85, 'Miércoles', '12:30:00', '14:30:00'),
(164, 86, 'Lunes', '08:00:00', '09:00:00'),
(165, 86, 'Jueves', '09:00:00', '12:30:00'),
(166, 87, 'Lunes', '07:00:00', '08:00:00'),
(167, 87, 'Viernes', '08:00:00', '11:30:00'),
(168, 88, 'Viernes', '11:30:00', '13:30:00'),
(169, 89, 'Viernes', '13:30:00', '14:30:00'),
(170, 78, 'Viernes', '07:00:00', '09:00:00'),
(171, 90, 'Lunes', '11:30:00', '12:30:00'),
(172, 90, 'Jueves', '10:30:00', '13:30:00'),
(173, 91, 'Lunes', '09:00:00', '11:30:00'),
(174, 91, 'Martes', '10:30:00', '12:30:00'),
(175, 92, 'Martes', '09:00:00', '10:00:00'),
(176, 92, 'Miércoles', '10:30:00', '12:30:00'),
(177, 92, 'Jueves', '13:30:00', '14:30:00'),
(178, 93, 'Lunes', '11:30:00', '13:30:00'),
(179, 94, 'Miércoles', '12:30:00', '14:30:00'),
(182, 67, 'Martes', '12:30:00', '13:30:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plantel`
--

DROP TABLE IF EXISTS `plantel`;
CREATE TABLE `plantel` (
  `id_plantel` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `plantel`
--

INSERT INTO `plantel` (`id_plantel`, `nombre`) VALUES
(11, 'CENTRO'),
(14, 'MILENIO'),
(17, 'PLAZA DEL SOL'),
(12, 'PUERTO VALLARTA'),
(23, 'TEPATITLÁN'),
(16, 'TESISTÁN'),
(15, 'TLAJOMULCO'),
(13, 'TORRE QUETZAL'),
(20, 'VALLARTA'),
(22, 'ZAPOPAN');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `supervision_agenda`
--

DROP TABLE IF EXISTS `supervision_agenda`;
CREATE TABLE `supervision_agenda` (
  `id_agenda` int(11) NOT NULL,
  `id_horario` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `supervision_hecha` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `supervision_agenda`
--

INSERT INTO `supervision_agenda` (`id_agenda`, `id_horario`, `fecha`, `supervision_hecha`) VALUES
(77, 87, '2024-06-25', 1),
(78, 90, '2024-06-26', 1),
(83, 93, '2024-06-26', 1),
(84, 61, '2024-07-01', 0),
(85, 64, '2024-07-01', 0),
(88, 96, '2024-07-05', 0),
(112, 82, '2024-08-26', 0),
(114, 116, '2024-08-27', 0),
(118, 125, '2024-08-26', 0),
(119, 130, '2024-08-28', 0),
(120, 182, '2024-08-27', 0),
(121, 132, '2024-08-27', 1),
(123, 135, '2024-08-29', 0),
(124, 137, '2024-08-26', 0),
(125, 143, '2024-08-28', 0),
(126, 152, '2024-08-29', 0),
(128, 169, '2024-08-30', 0),
(129, 139, '2024-09-02', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `supervision_criterio`
--

DROP TABLE IF EXISTS `supervision_criterio`;
CREATE TABLE `supervision_criterio` (
  `id_criterio` int(11) NOT NULL,
  `id_rubro` int(11) NOT NULL,
  `descripcion` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `supervision_criterio`
--

INSERT INTO `supervision_criterio` (`id_criterio`, `id_rubro`, `descripcion`) VALUES
(93, 251, 'Registro de asistencia'),
(94, 251, 'Se realiza evaluación diagnóstica'),
(95, 251, 'Se indica el tema'),
(96, 251, 'Se promueve el interés por el tema a tratar'),
(97, 251, 'Exposición de objetivos'),
(98, 251, 'Se retoman los contenidos de sesiones anteriores'),
(99, 252, 'Vigila el cumplimiento de la normatividad en las instalaciones de trabajo'),
(100, 252, 'Dominio del tema'),
(102, 252, 'Atención y concentración del grupo\n'),
(103, 252, 'Fomento de reflexión y argumentación'),
(104, 252, 'Lenguaje entendible, se aclaran los tecnicismos'),
(105, 252, 'Promoción de actividades relacionadas con la vida cotidiana'),
(106, 252, 'Empleo de estrategias didácticas (incluye tareas)'),
(107, 252, 'Claridad en las instrucciones'),
(108, 252, 'Supervisión de los problemas planteados'),
(109, 252, 'Genera ambiente propicio para la enseñanza y el aprendizaje'),
(110, 253, 'Evaluación del aprendizaje obtenido'),
(111, 253, 'Claridad en la conclusión de la clase'),
(112, 254, 'Puntualidad'),
(113, 254, 'Código de vestimenta'),
(114, 254, 'Entregó la planeación'),
(115, 254, 'Cumple con el avance de temas de acuerdo a la planeación'),
(116, 254, 'El tema presentado está dentro de la planeación'),
(117, 254, 'Mantiene actualizado el avance programático'),
(118, 254, 'Existe evidencia de que se enteró a los alumnos del encuadre'),
(119, 254, 'Se promueve la participación activa del grupo'),
(120, 254, 'Se promueve un ambiente de respeto entre los alumnos'),
(121, 254, 'Se promueven valores, mensajes positivos y estimulantes'),
(122, 254, 'Se retroalimenta y aclaran dudas'),
(123, 254, 'Mantiene la disciplina y limpieza en el aula'),
(124, 254, 'Administración del tiempo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `supervision_criterio_no_contable`
--

DROP TABLE IF EXISTS `supervision_criterio_no_contable`;
CREATE TABLE `supervision_criterio_no_contable` (
  `id_criterio` int(11) NOT NULL,
  `id_rubro` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `supervision_criterio_no_contable`
--

INSERT INTO `supervision_criterio_no_contable` (`id_criterio`, `id_rubro`, `descripcion`) VALUES
(1, 1, 'Método expositivo (Lección magistral)'),
(2, 1, 'Aprendizaje basado en problemas'),
(3, 1, 'Estudio de caso'),
(4, 1, 'Aprendizaje colaborativo'),
(5, 1, 'Aprendizaje orientado a proyectos'),
(6, 2, 'Memorístico'),
(7, 2, 'Significativo'),
(8, 2, 'Receptivo'),
(9, 2, 'Por descubrimiento'),
(10, 3, 'Trabajo en equipo'),
(11, 3, 'Trabajo individual'),
(12, 4, 'Cómic'),
(13, 4, 'Collage'),
(14, 4, 'Debate'),
(15, 4, 'Tríptico'),
(16, 4, 'Ensayo'),
(17, 4, 'Historieta'),
(18, 4, 'Resumen'),
(19, 4, 'Seminario'),
(20, 4, 'Paráfrasis'),
(21, 4, 'Investigación'),
(22, 4, 'Mapa mental'),
(23, 4, 'Mesa redonda'),
(24, 4, 'Lluvia de ideas'),
(25, 4, 'Análisis de caso'),
(26, 4, 'Dinámica grupal'),
(27, 4, 'Cuadro sinóptico'),
(28, 4, 'Ficha descriptiva'),
(29, 4, 'Mapa conceptual'),
(30, 4, 'Pregunta generadora'),
(31, 4, 'Cuadro de doble entrada'),
(32, 4, 'Resolución de ejercicios y problemas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `supervision_realizada`
--

DROP TABLE IF EXISTS `supervision_realizada`;
CREATE TABLE `supervision_realizada` (
  `id_supervision` int(11) NOT NULL,
  `id_agenda` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `tema` varchar(100) NOT NULL,
  `conclusion_general` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `supervision_realizada`
--

INSERT INTO `supervision_realizada` (`id_supervision`, `id_agenda`, `fecha`, `tema`, `conclusion_general`) VALUES
(40, 77, '2024-06-21 15:16:35', 'Introducción a la Radioactividad', 'La profesora muestra un dominio excelente del tema tratado. Demuestra una notable competencia y excelencia en la enseñanza. Se sugiere aclarar de mejor manera los tecnicismos utilizados en clase.'),
(41, 78, '2024-06-21 20:46:43', 'No indicado', ''),
(42, 83, '2024-06-21 20:58:03', 'No indicado', ''),
(45, 121, '2024-08-22 15:01:01', 'No indicado', '');

--
-- Disparadores `supervision_realizada`
--
DROP TRIGGER IF EXISTS `actualizar_supervision_agenda`;
DELIMITER $$
CREATE TRIGGER `actualizar_supervision_agenda` AFTER DELETE ON `supervision_realizada` FOR EACH ROW BEGIN

    UPDATE supervision_agenda

    SET supervision_hecha = FALSE

    WHERE id_agenda = OLD.id_agenda;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `supervision_realizada_contable_detalles`
--

DROP TABLE IF EXISTS `supervision_realizada_contable_detalles`;
CREATE TABLE `supervision_realizada_contable_detalles` (
  `id_supervision` int(11) NOT NULL,
  `id_criterio` int(11) NOT NULL,
  `criterio_cumplido` tinyint(4) NOT NULL,
  `comentario` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `supervision_realizada_contable_detalles`
--

INSERT INTO `supervision_realizada_contable_detalles` (`id_supervision`, `id_criterio`, `criterio_cumplido`, `comentario`) VALUES
(40, 93, 1, 'Registra a tiempo'),
(40, 94, 1, ''),
(40, 95, 1, ''),
(40, 96, 0, ''),
(40, 97, 1, ''),
(40, 98, 1, ''),
(40, 99, 1, ''),
(40, 100, 1, ''),
(40, 102, 0, ''),
(40, 103, 1, ''),
(40, 104, 0, 'Utiliza un lenguaje poco digerible para los estudiantes'),
(40, 105, 1, ''),
(40, 106, 1, ''),
(40, 107, 1, ''),
(40, 108, 1, ''),
(40, 109, 1, ''),
(40, 110, 1, ''),
(40, 111, 1, ''),
(40, 112, 1, ''),
(40, 113, 1, ''),
(40, 114, 1, ''),
(40, 115, 1, ''),
(40, 116, 1, ''),
(40, 117, 1, ''),
(40, 118, 1, ''),
(40, 119, 0, ''),
(40, 120, 1, ''),
(40, 121, 1, ''),
(40, 122, 0, ''),
(40, 123, 1, ''),
(40, 124, 1, ''),
(41, 93, 1, ''),
(41, 94, 1, ''),
(41, 95, 1, ''),
(41, 96, 1, ''),
(41, 97, 1, ''),
(41, 98, 1, ''),
(41, 99, 0, ''),
(41, 100, 1, ''),
(41, 102, 1, ''),
(41, 103, 0, ''),
(41, 104, 0, ''),
(41, 105, 1, ''),
(41, 106, 1, ''),
(41, 107, 1, ''),
(41, 108, 1, ''),
(41, 109, 1, ''),
(41, 110, 1, ''),
(41, 111, 1, ''),
(41, 112, 1, ''),
(41, 113, 1, ''),
(41, 114, 1, ''),
(41, 115, 0, ''),
(41, 116, 0, ''),
(41, 117, 1, ''),
(41, 118, 1, ''),
(41, 119, 1, ''),
(41, 120, 1, ''),
(41, 121, 1, ''),
(41, 122, 1, ''),
(41, 123, 1, ''),
(41, 124, 1, ''),
(42, 93, 1, ''),
(42, 94, 1, ''),
(42, 95, 1, ''),
(42, 96, 1, ''),
(42, 97, 1, ''),
(42, 98, 1, ''),
(42, 99, 1, ''),
(42, 100, 1, ''),
(42, 102, 1, ''),
(42, 103, 0, ''),
(42, 104, 1, ''),
(42, 105, 1, ''),
(42, 106, 1, ''),
(42, 107, 1, ''),
(42, 108, 1, ''),
(42, 109, 1, ''),
(42, 110, 1, ''),
(42, 111, 1, ''),
(42, 112, 1, ''),
(42, 113, 1, ''),
(42, 114, 1, ''),
(42, 115, 1, ''),
(42, 116, 0, ''),
(42, 117, 1, ''),
(42, 118, 1, ''),
(42, 119, 1, ''),
(42, 120, 0, ''),
(42, 121, 0, ''),
(42, 122, 0, ''),
(42, 123, 1, ''),
(42, 124, 1, ''),
(45, 93, 1, ''),
(45, 94, 1, ''),
(45, 95, 1, ''),
(45, 96, 1, ''),
(45, 97, 1, ''),
(45, 98, 1, ''),
(45, 99, 0, ''),
(45, 100, 0, ''),
(45, 102, 0, ''),
(45, 103, 0, ''),
(45, 104, 0, ''),
(45, 105, 0, ''),
(45, 106, 0, ''),
(45, 107, 0, ''),
(45, 108, 0, ''),
(45, 109, 0, ''),
(45, 110, 0, ''),
(45, 111, 0, ''),
(45, 112, 1, ''),
(45, 113, 1, ''),
(45, 114, 0, ''),
(45, 115, 0, ''),
(45, 116, 0, ''),
(45, 117, 0, ''),
(45, 118, 0, ''),
(45, 119, 0, ''),
(45, 120, 0, ''),
(45, 121, 1, ''),
(45, 122, 1, ''),
(45, 123, 0, ''),
(45, 124, 0, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `supervision_realizada_no_contable_detalles`
--

DROP TABLE IF EXISTS `supervision_realizada_no_contable_detalles`;
CREATE TABLE `supervision_realizada_no_contable_detalles` (
  `id_supervision` int(11) NOT NULL,
  `id_criterio` int(11) NOT NULL,
  `criterio_cumplido` tinyint(4) NOT NULL,
  `comentario` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `supervision_realizada_no_contable_detalles`
--

INSERT INTO `supervision_realizada_no_contable_detalles` (`id_supervision`, `id_criterio`, `criterio_cumplido`, `comentario`) VALUES
(40, 1, 1, ''),
(40, 2, 0, ''),
(40, 3, 0, ''),
(40, 4, 0, ''),
(40, 5, 0, ''),
(40, 6, 0, ''),
(40, 7, 0, ''),
(40, 8, 1, ''),
(40, 9, 0, ''),
(40, 10, 1, ''),
(40, 11, 0, ''),
(40, 12, 0, ''),
(40, 13, 0, ''),
(40, 14, 0, ''),
(40, 15, 0, ''),
(40, 16, 0, ''),
(40, 17, 0, ''),
(40, 18, 1, ''),
(40, 19, 0, ''),
(40, 20, 0, ''),
(40, 21, 1, ''),
(40, 22, 0, ''),
(40, 23, 0, ''),
(40, 24, 0, ''),
(40, 25, 0, ''),
(40, 26, 1, ''),
(40, 27, 0, ''),
(40, 28, 0, ''),
(40, 29, 0, ''),
(40, 30, 0, ''),
(40, 31, 0, ''),
(40, 32, 0, ''),
(41, 1, 1, ''),
(41, 2, 1, ''),
(41, 3, 1, ''),
(41, 4, 1, ''),
(41, 5, 1, ''),
(41, 6, 1, ''),
(41, 7, 0, ''),
(41, 8, 1, ''),
(41, 9, 1, ''),
(41, 10, 1, ''),
(41, 11, 1, ''),
(41, 12, 1, ''),
(41, 13, 1, ''),
(41, 14, 1, ''),
(41, 15, 0, ''),
(41, 16, 0, ''),
(41, 17, 0, ''),
(41, 18, 0, ''),
(41, 19, 0, ''),
(41, 20, 0, ''),
(41, 21, 0, ''),
(41, 22, 0, ''),
(41, 23, 0, ''),
(41, 24, 0, ''),
(41, 25, 0, ''),
(41, 26, 0, ''),
(41, 27, 0, ''),
(41, 28, 0, ''),
(41, 29, 0, ''),
(41, 30, 0, ''),
(41, 31, 0, ''),
(41, 32, 0, ''),
(42, 1, 0, ''),
(42, 2, 1, ''),
(42, 3, 1, ''),
(42, 4, 1, ''),
(42, 5, 1, ''),
(42, 6, 1, ''),
(42, 7, 0, ''),
(42, 8, 0, ''),
(42, 9, 0, ''),
(42, 10, 1, ''),
(42, 11, 0, ''),
(42, 12, 0, ''),
(42, 13, 1, ''),
(42, 14, 0, ''),
(42, 15, 0, ''),
(42, 16, 0, ''),
(42, 17, 0, ''),
(42, 18, 0, ''),
(42, 19, 0, ''),
(42, 20, 0, ''),
(42, 21, 0, ''),
(42, 22, 0, ''),
(42, 23, 0, ''),
(42, 24, 0, ''),
(42, 25, 0, ''),
(42, 26, 0, ''),
(42, 27, 0, ''),
(42, 28, 0, ''),
(42, 29, 0, ''),
(42, 30, 0, ''),
(42, 31, 0, ''),
(42, 32, 0, ''),
(45, 1, 0, ''),
(45, 2, 1, ''),
(45, 3, 1, ''),
(45, 4, 0, ''),
(45, 5, 1, ''),
(45, 6, 0, ''),
(45, 7, 0, ''),
(45, 8, 0, ''),
(45, 9, 0, ''),
(45, 10, 0, ''),
(45, 11, 0, ''),
(45, 12, 0, ''),
(45, 13, 0, ''),
(45, 14, 0, ''),
(45, 15, 0, ''),
(45, 16, 0, ''),
(45, 17, 0, ''),
(45, 18, 0, ''),
(45, 19, 0, ''),
(45, 20, 0, ''),
(45, 21, 0, ''),
(45, 22, 0, ''),
(45, 23, 0, ''),
(45, 24, 0, ''),
(45, 25, 0, ''),
(45, 26, 0, ''),
(45, 27, 0, ''),
(45, 28, 0, ''),
(45, 29, 0, ''),
(45, 30, 0, ''),
(45, 31, 0, ''),
(45, 32, 0, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `supervision_rubro`
--

DROP TABLE IF EXISTS `supervision_rubro`;
CREATE TABLE `supervision_rubro` (
  `id_rubro` int(11) NOT NULL,
  `descripcion` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `supervision_rubro`
--

INSERT INTO `supervision_rubro` (`id_rubro`, `descripcion`) VALUES
(253, 'CIERRE DE CLASE'),
(252, 'DESARROLLO DE CLASE'),
(254, 'DESEMPEÑO GENERAL'),
(251, 'INICIO DE CLASE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `supervision_rubro_no_contable`
--

DROP TABLE IF EXISTS `supervision_rubro_no_contable`;
CREATE TABLE `supervision_rubro_no_contable` (
  `id_rubro` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `supervision_rubro_no_contable`
--

INSERT INTO `supervision_rubro_no_contable` (`id_rubro`, `descripcion`) VALUES
(1, 'MÉTODOS DE ENSEÑANZA'),
(2, 'TIPO DE APRENDIZAJE'),
(3, 'MODO DE TRABAJO'),
(4, 'ESTRATEGIAS DE ENSEÑANZA EMPLEADAS');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `total_horas_materia`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `total_horas_materia`;
CREATE TABLE `total_horas_materia` (
`id_materia` int(11)
,`nombre` varchar(255)
,`total_horas` decimal(43,0)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `tipo_usuario` enum('Coordinador','Administrador') NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `apellidos` varchar(60) NOT NULL,
  `genero` enum('Masculino','Femenino') NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo_electronico` varchar(50) NOT NULL,
  `contrasenia` varchar(500) NOT NULL,
  `avatar` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `tipo_usuario`, `nombre`, `apellidos`, `genero`, `fecha_nacimiento`, `telefono`, `correo_electronico`, `contrasenia`, `avatar`) VALUES
(53, 'Administrador', 'Administrador', 'General', 'Masculino', '2024-06-21', '', 'administrador@universidad-une.com', '$2y$10$QNsfVw22yAKhTNZsKBcMPeIkvjg3rCfiRD2.cR1/MN5w6KqW93lVi', 'data:image/jpeg;base64,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'),
(60, 'Coordinador', 'Juan Carlos', 'González Aldana', 'Masculino', '2024-06-21', '', 'juancarlos.gonzalez@universidad-une.com', '$2y$10$YvMoLxaQc/NscHRkz6suJOT2UHDvob9i.2JIhLToW7TTiwhxpwWem', 'data:image/jpeg;base64,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');
INSERT INTO `usuario` (`id_usuario`, `tipo_usuario`, `nombre`, `apellidos`, `genero`, `fecha_nacimiento`, `telefono`, `correo_electronico`, `contrasenia`, `avatar`) VALUES
(64, 'Coordinador', 'Coordinador de', 'Carrera', 'Masculino', '1996-11-27', '3323618292', 'coordinadorde.carrera@universidad-une.com', '$2y$10$7KK8jzuTFxhHDF591oIKK.nI5iFvHICg9Q8jaBWeVQYgMWZ4BpHSy', 'data:image/jpeg;base64,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');

-- --------------------------------------------------------

--
-- Estructura para la vista `consultar_detalles_supervision_realizada`
--
DROP TABLE IF EXISTS `consultar_detalles_supervision_realizada`;

DROP VIEW IF EXISTS `consultar_detalles_supervision_realizada`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `consultar_detalles_supervision_realizada`  AS SELECT `sr`.`id_supervision` AS `id_supervision`, `sr`.`id_agenda` AS `id_agenda`, `sa`.`id_horario` AS `id_horario`, `sr`.`fecha` AS `fecha`, 'contable' AS `tipo_criterio`, `srub`.`id_rubro` AS `id_rubro`, `srub`.`descripcion` AS `rubro_descripcion`, `scrd`.`id_criterio` AS `id_criterio`, `sc`.`descripcion` AS `criterio_descripcion`, `scrd`.`criterio_cumplido` AS `criterio_cumplido`, `scrd`.`comentario` AS `comentario` FROM ((((`supervision_realizada` `sr` join `supervision_realizada_contable_detalles` `scrd` on(`sr`.`id_supervision` = `scrd`.`id_supervision`)) join `supervision_criterio` `sc` on(`scrd`.`id_criterio` = `sc`.`id_criterio`)) join `supervision_rubro` `srub` on(`sc`.`id_rubro` = `srub`.`id_rubro`)) join `supervision_agenda` `sa` on(`sr`.`id_agenda` = `sa`.`id_agenda`))union all select `sr`.`id_supervision` AS `id_supervision`,`sr`.`id_agenda` AS `id_agenda`,`sa`.`id_horario` AS `id_horario`,`sr`.`fecha` AS `fecha`,'no_contable' AS `tipo_criterio`,`srubnc`.`id_rubro` AS `id_rubro`,`srubnc`.`descripcion` AS `rubro_descripcion`,`sncrd`.`id_criterio` AS `id_criterio`,`snc`.`descripcion` AS `criterio_descripcion`,`sncrd`.`criterio_cumplido` AS `criterio_cumplido`,`sncrd`.`comentario` AS `comentario` from ((((`supervision_realizada` `sr` join `supervision_realizada_no_contable_detalles` `sncrd` on(`sr`.`id_supervision` = `sncrd`.`id_supervision`)) join `supervision_criterio_no_contable` `snc` on(`sncrd`.`id_criterio` = `snc`.`id_criterio`)) join `supervision_rubro_no_contable` `srubnc` on(`snc`.`id_rubro` = `srubnc`.`id_rubro`)) join `supervision_agenda` `sa` on(`sr`.`id_agenda` = `sa`.`id_agenda`))  ;

-- --------------------------------------------------------

--
-- Estructura para la vista `consultar_supervision`
--
DROP TABLE IF EXISTS `consultar_supervision`;

DROP VIEW IF EXISTS `consultar_supervision`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `consultar_supervision`  AS SELECT `sr`.`id_supervision` AS `id_supervision`, `sr`.`fecha` AS `fecha_supervision`, `sr`.`tema` AS `tema`, `sr`.`conclusion_general` AS `conclusion_general`, `sa`.`id_agenda` AS `id_agenda`, `sa`.`fecha` AS `fecha_agenda`, `sa`.`supervision_hecha` AS `supervision_hecha`, `mh`.`id_horario` AS `id_horario`, `mh`.`dia_semana` AS `dia_semana`, `mh`.`hora_inicio` AS `hora_inicio`, `mh`.`hora_fin` AS `hora_fin`, `m`.`id_materia` AS `id_materia`, `m`.`nombre` AS `nombre_materia`, `d`.`id_docente` AS `id_docente`, `d`.`nombre` AS `nombre_docente`, `d`.`apellidos` AS `apellido_docente`, `c`.`id_carrera` AS `id_carrera`, concat(`c`.`tipo`,' en ',`c`.`nombre`) AS `nombre_carrera`, `p`.`id_plantel` AS `id_plantel`, `p`.`nombre` AS `nombre_plantel` FROM ((((((`supervision_realizada` `sr` join `supervision_agenda` `sa` on(`sr`.`id_agenda` = `sa`.`id_agenda`)) join `materia_horarios` `mh` on(`sa`.`id_horario` = `mh`.`id_horario`)) join `materia` `m` on(`mh`.`id_materia` = `m`.`id_materia`)) join `carrera` `c` on(`m`.`id_carrera` = `c`.`id_carrera`)) join `plantel` `p` on(`m`.`id_plantel` = `p`.`id_plantel`)) join `docente` `d` on(`m`.`id_docente` = `d`.`id_docente`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `coordinador_usuario`
--
DROP TABLE IF EXISTS `coordinador_usuario`;

DROP VIEW IF EXISTS `coordinador_usuario`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `coordinador_usuario`  AS SELECT `usuario`.`id_usuario` AS `id_usuario`, `coordinador`.`id_coordinador` AS `id_coordinador`, `usuario`.`nombre` AS `nombre`, `usuario`.`apellidos` AS `apellidos`, `usuario`.`correo_electronico` AS `correo_electronico`, `usuario`.`avatar` AS `avatar`, group_concat(`carrera`.`tipo`,' ',`carrera`.`nombre` separator ', ') AS `carreras_coordina`, `usuario`.`fecha_nacimiento` AS `fecha_nacimiento`, `usuario`.`genero` AS `genero`, `usuario`.`telefono` AS `telefono` FROM (((`usuario` left join `coordinador` on(`usuario`.`id_usuario` = `coordinador`.`id_usuario`)) left join `carrera_coordinador` on(`coordinador`.`id_coordinador` = `carrera_coordinador`.`id_coordinador`)) left join `carrera` on(`carrera_coordinador`.`id_carrera` = `carrera`.`id_carrera`)) WHERE `usuario`.`tipo_usuario` = 'Coordinador' GROUP BY `usuario`.`id_usuario` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `listar_agenda_supervision`
--
DROP TABLE IF EXISTS `listar_agenda_supervision`;

DROP VIEW IF EXISTS `listar_agenda_supervision`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `listar_agenda_supervision`  AS SELECT `coord`.`id_coordinador` AS `id_coordinador`, `sa`.`id_agenda` AS `id_agenda`, concat(`d`.`nombre`,' ',`d`.`apellidos`) AS `nombre_docente`, `sa`.`fecha` AS `fecha`, `mh`.`dia_semana` AS `dia_semana`, `mh`.`hora_inicio` AS `hora_inicio`, `mh`.`hora_fin` AS `hora_fin`, `m`.`nombre` AS `nombre_materia`, `m`.`grupo` AS `grupo_materia`, concat(`coord`.`nombre`,' ',`coord`.`apellidos`) AS `nombre_coordinador`, if(`sa`.`supervision_hecha`,'Realizada','Sin realizarse') AS `status`, `carrera`.`id_carrera` AS `id_carrera`, concat(`carrera`.`tipo`,' en ',`carrera`.`nombre`) AS `carrera`, `p`.`id_plantel` AS `id_plantel`, `p`.`nombre` AS `plantel` FROM ((((((`supervision_agenda` `sa` join `materia_horarios` `mh` on(`sa`.`id_horario` = `mh`.`id_horario`)) join `materia` `m` on(`mh`.`id_materia` = `m`.`id_materia`)) join `plantel` `p` on(`m`.`id_plantel` = `p`.`id_plantel`)) join `carrera` on(`m`.`id_carrera` = `carrera`.`id_carrera`)) join `docente` `d` on(`m`.`id_docente` = `d`.`id_docente`)) join `coordinador_usuario` `coord` on(`d`.`id_coordinador` = `coord`.`id_coordinador`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `listar_carrera_detalles`
--
DROP TABLE IF EXISTS `listar_carrera_detalles`;

DROP VIEW IF EXISTS `listar_carrera_detalles`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `listar_carrera_detalles`  AS SELECT `carrera`.`id_carrera` AS `id_carrera`, `carrera`.`tipo` AS `tipo`, `carrera`.`nombre` AS `nombre`, ifnull(`usuario`.`nombre`,'No asignado') AS `coordinador_nombre`, ifnull(`usuario`.`correo_electronico`,'No asignado') AS `coordinador_correo`, ifnull(`coordinador`.`id_coordinador`,'No asignado') AS `id_coordinador`, ifnull(`coordinador`.`id_usuario`,'No asignado') AS `id_usuario_coordinador`, group_concat(`plantel`.`nombre` separator ', ') AS `planteles` FROM (((((`carrera` left join `carrera_coordinador` on(`carrera`.`id_carrera` = `carrera_coordinador`.`id_carrera`)) left join `coordinador` on(`carrera_coordinador`.`id_coordinador` = `coordinador`.`id_coordinador`)) left join `usuario` on(`coordinador`.`id_usuario` = `usuario`.`id_usuario`)) left join `carrera_plantel` on(`carrera`.`id_carrera` = `carrera_plantel`.`id_carrera`)) left join `plantel` on(`carrera_plantel`.`id_plantel` = `plantel`.`id_plantel`)) GROUP BY `carrera`.`id_carrera` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `listar_criterios_supervision_por_rubro`
--
DROP TABLE IF EXISTS `listar_criterios_supervision_por_rubro`;

DROP VIEW IF EXISTS `listar_criterios_supervision_por_rubro`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `listar_criterios_supervision_por_rubro`  AS SELECT `r`.`id_rubro` AS `id_rubro`, `r`.`descripcion` AS `rubro_descripcion`, `c`.`id_criterio` AS `id_criterio`, `c`.`descripcion` AS `descripcion_criterio` FROM (`supervision_rubro` `r` left join `supervision_criterio` `c` on(`r`.`id_rubro` = `c`.`id_rubro`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `listar_criterios_supervision_por_rubro_no_contable`
--
DROP TABLE IF EXISTS `listar_criterios_supervision_por_rubro_no_contable`;

DROP VIEW IF EXISTS `listar_criterios_supervision_por_rubro_no_contable`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `listar_criterios_supervision_por_rubro_no_contable`  AS SELECT `r`.`id_rubro` AS `id_rubro`, `r`.`descripcion` AS `rubro_descripcion`, `c`.`id_criterio` AS `id_criterio`, `c`.`descripcion` AS `descripcion_criterio` FROM (`supervision_rubro_no_contable` `r` left join `supervision_criterio_no_contable` `c` on(`r`.`id_rubro` = `c`.`id_rubro`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `listar_docente_materias_horarios`
--
DROP TABLE IF EXISTS `listar_docente_materias_horarios`;

DROP VIEW IF EXISTS `listar_docente_materias_horarios`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `listar_docente_materias_horarios`  AS SELECT `d`.`id_coordinador` AS `id_coordinador`, CASE WHEN `sa_agendado`.`id_docente` is not null THEN 1 ELSE 0 END AS `es_profesor_agendado`, coalesce(`sa_agendado`.`id_agenda`,`sa`.`id_agenda`) AS `id_agenda`, CASE WHEN `sa_agendado`.`supervision_hecha` = 1 THEN 1 ELSE 0 END AS `supervision_hecha`, `d`.`id_docente` AS `id_docente`, `d`.`nombre` AS `nombre_docente`, `d`.`apellidos` AS `apellido_docente`, `d`.`correo_electronico` AS `correo_electronico`, `d`.`perfil_profesional` AS `perfil_profesional`, `m`.`id_carrera` AS `id_carrera`, `m`.`id_plantel` AS `id_plantel`, `plantel`.`nombre` AS `nombre_plantel`, `m`.`nombre` AS `nombre_materia`, `m`.`grupo` AS `grupo_materia`, `m`.`id_materia` AS `id_materia`, `mh`.`id_horario` AS `id_horario`, `mh`.`dia_semana` AS `dia_semana`, `mh`.`hora_inicio` AS `hora_inicio`, `mh`.`hora_fin` AS `hora_fin`, `horas`.`total_horas` AS `total_horas`, CASE WHEN `sa`.`id_horario` is not null THEN 1 ELSE 0 END AS `es_horario_agendado`, `sa`.`fecha` AS `fecha` FROM ((((((`docente` `d` join `materia` `m` on(`d`.`id_docente` = `m`.`id_docente`)) join `materia_horarios` `mh` on(`m`.`id_materia` = `mh`.`id_materia`)) left join `supervision_agenda` `sa` on(`mh`.`id_horario` = `sa`.`id_horario`)) left join (select `d`.`id_docente` AS `id_docente`,max(`sa`.`id_agenda`) AS `id_agenda`,max(`sa`.`supervision_hecha`) AS `supervision_hecha` from ((((`docente` `d` join `materia` `m` on(`d`.`id_docente` = `m`.`id_docente`)) join `materia_horarios` `mh` on(`m`.`id_materia` = `mh`.`id_materia`)) join `supervision_agenda` `sa` on(`mh`.`id_horario` = `sa`.`id_horario`)) join `plantel` on(`m`.`id_plantel` = `plantel`.`id_plantel`)) group by `d`.`id_docente`) `sa_agendado` on(`d`.`id_docente` = `sa_agendado`.`id_docente`)) join `plantel` on(`m`.`id_plantel` = `plantel`.`id_plantel`)) join `total_horas_materia` `horas` on(`horas`.`id_materia` = `m`.`id_materia`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `lista_carreras_sin_coordinador`
--
DROP TABLE IF EXISTS `lista_carreras_sin_coordinador`;

DROP VIEW IF EXISTS `lista_carreras_sin_coordinador`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `lista_carreras_sin_coordinador`  AS SELECT `carrera`.`id_carrera` AS `id_carrera`, concat(`carrera`.`tipo`,' ',`carrera`.`nombre`) AS `nombre`, `carrera`.`tipo` AS `tipo` FROM (`carrera` left join `carrera_coordinador` on(`carrera`.`id_carrera` = `carrera_coordinador`.`id_carrera`)) WHERE `carrera_coordinador`.`id_carrera` is null ;

-- --------------------------------------------------------

--
-- Estructura para la vista `total_horas_materia`
--
DROP TABLE IF EXISTS `total_horas_materia`;

DROP VIEW IF EXISTS `total_horas_materia`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `total_horas_materia`  AS SELECT `m`.`id_materia` AS `id_materia`, `m`.`nombre` AS `nombre`, floor(sum(timestampdiff(MINUTE,`h`.`hora_inicio`,`h`.`hora_fin`)) / 60) AS `total_horas` FROM (`materia_horarios` `h` join `materia` `m` on(`m`.`id_materia` = `h`.`id_materia`)) GROUP BY `m`.`id_materia` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id_administrador`);

--
-- Indices de la tabla `carrera`
--
ALTER TABLE `carrera`
  ADD PRIMARY KEY (`id_carrera`),
  ADD UNIQUE KEY `nombre_UNIQUE` (`nombre`);

--
-- Indices de la tabla `carrera_coordinador`
--
ALTER TABLE `carrera_coordinador`
  ADD PRIMARY KEY (`id_carrera`,`id_coordinador`),
  ADD KEY `carrera_coordinador_ibfk_2` (`id_coordinador`);

--
-- Indices de la tabla `carrera_plantel`
--
ALTER TABLE `carrera_plantel`
  ADD PRIMARY KEY (`id_carrera`,`id_plantel`),
  ADD UNIQUE KEY `unique_index` (`id_carrera`,`id_plantel`),
  ADD KEY `carrera_plantel_ibfk_2` (`id_plantel`);

--
-- Indices de la tabla `config_usuario`
--
ALTER TABLE `config_usuario`
  ADD PRIMARY KEY (`id_config`),
  ADD KEY `fk_usuario_idx` (`id_usuario`);

--
-- Indices de la tabla `coordinador`
--
ALTER TABLE `coordinador`
  ADD PRIMARY KEY (`id_coordinador`),
  ADD KEY `id_administrador` (`id_administrador`),
  ADD KEY `coordinador_usuario_fk_idx` (`id_usuario`);

--
-- Indices de la tabla `docente`
--
ALTER TABLE `docente`
  ADD PRIMARY KEY (`id_docente`),
  ADD UNIQUE KEY `id_coordinador_UNIQUE` (`nombre`,`apellidos`,`id_coordinador`),
  ADD KEY `id_coordinador` (`id_coordinador`);

--
-- Indices de la tabla `materia`
--
ALTER TABLE `materia`
  ADD PRIMARY KEY (`id_materia`),
  ADD KEY `fk_id_docente_idx` (`id_docente`),
  ADD KEY `fk_id_carrera_idx` (`id_carrera`),
  ADD KEY `fk_id_plantel_materia_idx` (`id_plantel`);

--
-- Indices de la tabla `materia_horarios`
--
ALTER TABLE `materia_horarios`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `materia_horarios_ibfk_1` (`id_materia`);

--
-- Indices de la tabla `plantel`
--
ALTER TABLE `plantel`
  ADD PRIMARY KEY (`id_plantel`),
  ADD UNIQUE KEY `nombre_UNIQUE` (`nombre`);

--
-- Indices de la tabla `supervision_agenda`
--
ALTER TABLE `supervision_agenda`
  ADD PRIMARY KEY (`id_agenda`),
  ADD UNIQUE KEY `id_horario_UNIQUE` (`id_horario`),
  ADD KEY `fk_agenda_materia_idx` (`id_horario`);

--
-- Indices de la tabla `supervision_criterio`
--
ALTER TABLE `supervision_criterio`
  ADD PRIMARY KEY (`id_criterio`),
  ADD UNIQUE KEY `descripcion_UNIQUE` (`descripcion`),
  ADD KEY `fk_id_rubro_criterio_idx` (`id_rubro`);

--
-- Indices de la tabla `supervision_criterio_no_contable`
--
ALTER TABLE `supervision_criterio_no_contable`
  ADD PRIMARY KEY (`id_criterio`),
  ADD KEY `fk_id_rubro_no_contable_idx` (`id_rubro`);

--
-- Indices de la tabla `supervision_realizada`
--
ALTER TABLE `supervision_realizada`
  ADD PRIMARY KEY (`id_supervision`),
  ADD UNIQUE KEY `id_agenda_UNIQUE` (`id_agenda`),
  ADD KEY `fk_id_agenda_idx` (`id_agenda`);

--
-- Indices de la tabla `supervision_realizada_contable_detalles`
--
ALTER TABLE `supervision_realizada_contable_detalles`
  ADD KEY `fk_id_supervision_idx` (`id_supervision`),
  ADD KEY `fk_id_criterio_idx` (`id_criterio`);

--
-- Indices de la tabla `supervision_realizada_no_contable_detalles`
--
ALTER TABLE `supervision_realizada_no_contable_detalles`
  ADD KEY `fk_id_supervision_no_contable_idx` (`id_supervision`),
  ADD KEY `fk_id_criterio_no_contable_idx` (`id_criterio`);

--
-- Indices de la tabla `supervision_rubro`
--
ALTER TABLE `supervision_rubro`
  ADD PRIMARY KEY (`id_rubro`),
  ADD UNIQUE KEY `descripcion_UNIQUE` (`descripcion`);

--
-- Indices de la tabla `supervision_rubro_no_contable`
--
ALTER TABLE `supervision_rubro_no_contable`
  ADD PRIMARY KEY (`id_rubro`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo_electronico_UNIQUE` (`correo_electronico`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administrador`
--
ALTER TABLE `administrador`
  MODIFY `id_administrador` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `carrera`
--
ALTER TABLE `carrera`
  MODIFY `id_carrera` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT de la tabla `config_usuario`
--
ALTER TABLE `config_usuario`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `coordinador`
--
ALTER TABLE `coordinador`
  MODIFY `id_coordinador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `docente`
--
ALTER TABLE `docente`
  MODIFY `id_docente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT de la tabla `materia`
--
ALTER TABLE `materia`
  MODIFY `id_materia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT de la tabla `materia_horarios`
--
ALTER TABLE `materia_horarios`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT de la tabla `plantel`
--
ALTER TABLE `plantel`
  MODIFY `id_plantel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `supervision_agenda`
--
ALTER TABLE `supervision_agenda`
  MODIFY `id_agenda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT de la tabla `supervision_criterio`
--
ALTER TABLE `supervision_criterio`
  MODIFY `id_criterio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT de la tabla `supervision_criterio_no_contable`
--
ALTER TABLE `supervision_criterio_no_contable`
  MODIFY `id_criterio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `supervision_realizada`
--
ALTER TABLE `supervision_realizada`
  MODIFY `id_supervision` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `supervision_rubro`
--
ALTER TABLE `supervision_rubro`
  MODIFY `id_rubro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=255;

--
-- AUTO_INCREMENT de la tabla `supervision_rubro_no_contable`
--
ALTER TABLE `supervision_rubro_no_contable`
  MODIFY `id_rubro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrera_coordinador`
--
ALTER TABLE `carrera_coordinador`
  ADD CONSTRAINT `carrera_coordinador_ibfk_1` FOREIGN KEY (`id_carrera`) REFERENCES `carrera` (`id_carrera`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `carrera_coordinador_ibfk_2` FOREIGN KEY (`id_coordinador`) REFERENCES `coordinador` (`id_coordinador`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `carrera_plantel`
--
ALTER TABLE `carrera_plantel`
  ADD CONSTRAINT `carrera_plantel_ibfk_1` FOREIGN KEY (`id_carrera`) REFERENCES `carrera` (`id_carrera`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `carrera_plantel_ibfk_2` FOREIGN KEY (`id_plantel`) REFERENCES `plantel` (`id_plantel`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `config_usuario`
--
ALTER TABLE `config_usuario`
  ADD CONSTRAINT `fk_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `coordinador`
--
ALTER TABLE `coordinador`
  ADD CONSTRAINT `coordinador_usuario_fk` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `docente`
--
ALTER TABLE `docente`
  ADD CONSTRAINT `docente_ibfk_1` FOREIGN KEY (`id_coordinador`) REFERENCES `coordinador` (`id_coordinador`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materia`
--
ALTER TABLE `materia`
  ADD CONSTRAINT `fk_id_carrera` FOREIGN KEY (`id_carrera`) REFERENCES `carrera` (`id_carrera`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_id_docente_materia` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`id_docente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_id_plantel_materia` FOREIGN KEY (`id_plantel`) REFERENCES `plantel` (`id_plantel`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materia_horarios`
--
ALTER TABLE `materia_horarios`
  ADD CONSTRAINT `materia_horarios_ibfk_1` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `supervision_agenda`
--
ALTER TABLE `supervision_agenda`
  ADD CONSTRAINT `fk_agenda_materia` FOREIGN KEY (`id_horario`) REFERENCES `materia_horarios` (`id_horario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `supervision_criterio`
--
ALTER TABLE `supervision_criterio`
  ADD CONSTRAINT `fk_id_rubro_criterio` FOREIGN KEY (`id_rubro`) REFERENCES `supervision_rubro` (`id_rubro`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `supervision_criterio_no_contable`
--
ALTER TABLE `supervision_criterio_no_contable`
  ADD CONSTRAINT `fk_id_rubro_no_contable` FOREIGN KEY (`id_rubro`) REFERENCES `supervision_rubro_no_contable` (`id_rubro`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `supervision_realizada`
--
ALTER TABLE `supervision_realizada`
  ADD CONSTRAINT `fk_id_agenda` FOREIGN KEY (`id_agenda`) REFERENCES `supervision_agenda` (`id_agenda`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `supervision_realizada_contable_detalles`
--
ALTER TABLE `supervision_realizada_contable_detalles`
  ADD CONSTRAINT `fk_id_criterio` FOREIGN KEY (`id_criterio`) REFERENCES `supervision_criterio` (`id_criterio`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_id_supervision` FOREIGN KEY (`id_supervision`) REFERENCES `supervision_realizada` (`id_supervision`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `supervision_realizada_no_contable_detalles`
--
ALTER TABLE `supervision_realizada_no_contable_detalles`
  ADD CONSTRAINT `fk_id_criterio_no_contable` FOREIGN KEY (`id_criterio`) REFERENCES `supervision_criterio_no_contable` (`id_criterio`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_id_supervision_no_contable` FOREIGN KEY (`id_supervision`) REFERENCES `supervision_realizada` (`id_supervision`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
