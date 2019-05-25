-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3307
-- Tiempo de generación: 25-05-2019 a las 19:36:19
-- Versión del servidor: 5.7.19
-- Versión de PHP: 7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `geollin_concurso_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `ID_CATEGORIA` int(11) NOT NULL,
  `CATEGORIA` varchar(128) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`ID_CATEGORIA`, `CATEGORIA`) VALUES
(1, 'GEOFÍSICA'),
(2, 'GEOLOGÍA'),
(3, 'PETROLEROS'),
(4, 'GENERALES'),
(5, 'DESEMPATE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_etapa`
--

CREATE TABLE `categorias_etapa` (
  `ID_CAT_ETAPA` int(11) NOT NULL,
  `ID_ETAPA` int(3) DEFAULT NULL,
  `ID_CATEGORIA` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `categorias_etapa`
--

INSERT INTO `categorias_etapa` (`ID_CAT_ETAPA`, `ID_ETAPA`, `ID_CATEGORIA`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 2, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `concursantes`
--

CREATE TABLE `concursantes` (
  `ID_CONCURSANTE` int(11) NOT NULL,
  `CONCURSANTE` varchar(512) DEFAULT '',
  `PASSWORD` varchar(128) DEFAULT '',
  `ID_CONCURSO` int(6) NOT NULL,
  `CONCURSANTE_POSICION` int(6) DEFAULT '0',
  `INICIO_SESION` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `concursantes`
--

INSERT INTO `concursantes` (`ID_CONCURSANTE`, `CONCURSANTE`, `PASSWORD`, `ID_CONCURSO`, `CONCURSANTE_POSICION`, `INICIO_SESION`) VALUES
(1, 'andrew', 'andrew', 1, 1, 0),
(2, 'ipad', 'ipad', 1, 2, 0),
(3, 'lg', 'lg', 1, 3, 0),
(4, 'nyx', 'nyx', 1, 4, 0),
(5, 'andrew', 'andrew', 2, 1, 0),
(6, 'ipad', 'ipad', 2, 2, 0),
(7, 'lg', 'lg', 2, 3, 0),
(8, 'nyx', 'nyx', 2, 4, 0),
(9, 'andrew', 'andrew', 3, 1, 0),
(10, 'alan', 'alan', 3, 2, 0),
(11, 'camila', 'camila', 3, 3, 0),
(12, 'Daniela', 'pedorra', 4, 1, 1),
(13, 'dany', 'dany', 5, 1, 0),
(14, 'dany', 'dany', 6, 1, 1),
(15, 'dany', 'dany', 7, 1, 0),
(16, 'dany', 'dany', 8, 1, 1),
(17, 'a', 'a', 9, 1, 1),
(18, 'andrew', 'a', 10, 1, 1),
(19, 'mama', 'a', 10, 2, 1),
(20, 'mio', 'a', 10, 3, 1),
(21, 'ipad', 'a', 10, 4, 1),
(22, 'tablet', 'a', 10, 5, 1),
(23, 'laptop', 'a', 10, 6, 1),
(24, 'dany', 'a', 10, 7, 1),
(25, 'Andrew', 'andrew', 11, 1, 0),
(26, 'Andrew', 'andrew', 12, 1, 1),
(27, 'ipad', 'ipad', 13, 1, 1),
(28, 'cel', 'cel', 13, 2, 1),
(29, 'yair', 'yair', 14, 1, 1),
(30, 'andrew', 'andrew', 14, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `concursos`
--

CREATE TABLE `concursos` (
  `ID_CONCURSO` int(11) NOT NULL,
  `CONCURSO` varchar(1024) DEFAULT '',
  `ID_ETAPA` int(3) NOT NULL,
  `FECHA_INICIO` datetime DEFAULT NULL,
  `FECHA_CIERRE` datetime DEFAULT NULL,
  `ID_RONDA` int(3) DEFAULT NULL,
  `ID_CATEGORIA` int(3) DEFAULT NULL,
  `NIVEL_EMPATE` int(3) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `concursos`
--

INSERT INTO `concursos` (`ID_CONCURSO`, `CONCURSO`, `ID_ETAPA`, `FECHA_INICIO`, `FECHA_CIERRE`, `ID_RONDA`, `ID_CATEGORIA`, `NIVEL_EMPATE`) VALUES
(1, 'Prueba 1 - 4concursantes -Geofisica', 1, '2019-05-18 21:19:49', NULL, 1, 1, 0),
(2, 'Prueba 1 - 4concursantes -Geofisica', 1, '2019-05-18 21:21:27', NULL, 1, 1, 0),
(3, 'TEST', 1, '2019-05-18 22:36:48', NULL, 1, 1, 0),
(4, 'Individual test A', 1, '2019-05-19 20:14:42', NULL, 2, 1, 0),
(5, 'Test 2 Individual Petrolero', 1, '2019-05-19 21:39:40', NULL, 1, 3, 0),
(6, 'Test 2 Individual Petrolero', 1, '2019-05-19 21:39:50', NULL, 1, 3, 0),
(7, 'Test 2 Individual Petrolero', 1, '2019-05-19 21:40:43', NULL, 1, 3, 0),
(8, 'Test 3 petroleros', 1, '2019-05-19 21:45:05', '2019-05-19 22:33:25', 2, 3, 0),
(9, 'Prueba', 1, '2019-05-19 22:33:36', '2019-05-19 22:36:30', 2, 1, 0),
(10, 'Prueba general 1- 7 dispositivos', 1, '2019-05-19 22:43:50', '2019-05-19 23:03:56', 3, 2, 2),
(11, 'Prueba puntajes descenas', 1, '2019-05-25 18:57:09', NULL, 1, 1, 0),
(12, 'Prueba puntajes descenas', 1, '2019-05-25 18:58:15', '2019-05-25 19:10:17', 2, 1, 0),
(13, 'Concurso tens empate individual', 1, '2019-05-25 19:10:53', '2019-05-25 19:17:27', 3, 3, 1),
(14, 'Grupal puntajes por 10', 2, '2019-05-25 19:17:59', '2019-05-25 19:32:51', 5, 4, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etapas_tipo_concurso`
--

CREATE TABLE `etapas_tipo_concurso` (
  `ID_ETAPA` int(6) NOT NULL,
  `ETAPA` varchar(128) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `etapas_tipo_concurso`
--

INSERT INTO `etapas_tipo_concurso` (`ID_ETAPA`, `ETAPA`) VALUES
(1, 'INDIVIDUAL'),
(2, 'GRUPAL');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grados_dificultad`
--

CREATE TABLE `grados_dificultad` (
  `ID_GRADO` int(6) NOT NULL,
  `DIFICULTAD` varchar(128) DEFAULT '',
  `PUNTAJE` int(3) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `grados_dificultad`
--

INSERT INTO `grados_dificultad` (`ID_GRADO`, `DIFICULTAD`, `PUNTAJE`) VALUES
(1, 'BAJA', 10),
(2, 'MEDIA', 20),
(3, 'ALTA', 30);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas`
--

CREATE TABLE `preguntas` (
  `ID_PREGUNTA` int(6) NOT NULL,
  `PREGUNTA` varchar(2048) DEFAULT '',
  `ID_GRADO` int(2) NOT NULL,
  `ID_CATEGORIA` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `preguntas`
--

INSERT INTO `preguntas` (`ID_PREGUNTA`, `PREGUNTA`, `ID_GRADO`, `ID_CATEGORIA`) VALUES
(1, 'En la programación de la sísmica tridimensional, ¿cuál es el parámetro más importante?', 2, 1),
(2, 'En el análisis de señales continuas, a la representación gráfica de una transformada de Fourier se le denomina espectro de amplitudes a:', 3, 1),
(3, '¿Qué fenómeno afecta en mayor grado la calidad de las lecturas en los levantamientos magnéticos?', 2, 1),
(4, '¿Qué fenómeno afecta en mayor grado la calidad de las lecturas en los levantamientos gravimétricos?', 2, 1),
(5, '¿Cómo se le llama al tren de ondas generadas por la fuente de energía Vibros y que se propaga al interior del subsuelo?', 1, 1),
(6, 'El sismograma grabado es correlacionado con la señal de referencia que maneja electrónicamente el vibrador para producir un punto de tiro, a la forma de onda simétrica se le conoce cómo:', 3, 1),
(7, '¿Cuál es la prueba de campo que se utiliza para determinar los parámetros óptimos de la adquisición 2D?', 1, 1),
(8, '¿Qué unidades se utilizan en la prospección magnetométrica?', 1, 1),
(9, '¿Cuál es el rango de frecuencias que se utilizan en el método electromagnético?', 2, 1),
(10, '¿Qué propiedades de las rocas influyen más en el modelado de datos magnéticos?', 1, 1),
(11, '¿Cuál es la superficie equipotencial de la Tierra que coincide con el nivel medio de la superficie libre de los océanos, corrigiendo por el efecto de las mareas?', 2, 1),
(12, '¿Dónde es mayor la gravedad en la tierra?', 2, 1),
(13, 'Módulo elástico que mide la relación del esfuerzo-deformación en condiciones de elongación o compresión:', 1, 1),
(14, 'Describa cual es el principal efecto astronómico en la deriva instrumental', 1, 1),
(15, 'Tipo de velocidad usada para corregir dinámicamente los datos sísmicos dentro del procesado:', 2, 1),
(16, 'Temperatura por encima de la cual un material pierde su magnetización:', 1, 1),
(17, '¿Dónde se forma el campo magnético terrestre?', 1, 1),
(18, 'Las anomalías gravimétricas permiten revelar la topografía del interior de la tierra, con ¿cuál de ellas se puede comprobar la existencia de la compensación isostática en las zonas montañosas?', 3, 1),
(19, 'Un material con susceptibilidad magnética débil y positiva se conoce como Paramagnético.', 1, 1),
(20, 'Los arreglos de Geófonos pueden diseñarse para reducir el ruido a expensas del ancho de banda.', 1, 1),
(21, 'En prospección magnetométrica, ¿a cuántos Gauss equivale una Tesla?', 2, 1),
(22, 'Un filtro convolutivo en el dominio del tiempo es visto como:', 2, 1),
(23, 'El método de exploración geofísica \"GPR\" opera en el dominio de las bajas frecuencias.', 1, 1),
(24, 'Es la frecuencia de muestreo óptima para reconstruir una señal sin pérdidas significativas de información.', 2, 1),
(25, 'A la propiedad elástica que define la característica litológica de un determinado medio y es el producto de la densidad por la velocidad se le conoce como:', 1, 1),
(26, 'Cuando la transmisión de la energía elástica es a través del movimiento en dirección transversal a la superficie y perpendicular a la dirección de propagación, el tipo de onda es:', 1, 1),
(27, '¿Qué principio dice que todo punto de una onda puede considerarse como centro de excitación de nuevas ondas y que la superficie envolvente común a esas ondas elementales representa la onda real o Oprincipal?', 1, 1),
(28, '¿Qué tipo de ruido sísmico coherente es generado por la fuente de energía y que es de propagación vertical?', 2, 1),
(29, 'Es la representación matemática de una señal que pasa a través de otra llamada filtro:', 2, 1),
(30, 'En el análisis de señales continuas, a la representación gráfica de una transformada de Fourier se le denomina espectro de fase a:', 3, 1),
(31, '¿Con qué registro de pozo se debe uno apoyar para decidir tomar un registro de ruidos?', 3, 1),
(32, 'Es un tipo de atributo sísmico instantáneo:', 2, 1),
(33, '¿Qué fuente de energía sísmica utilizaría preferentemente en áreas marinas?', 1, 1),
(34, '¿Qué unidades se utilizan en el método electromagnético?', 1, 1),
(35, 'Es la representación matemática necesaria para eliminar el efecto del filtrado:', 2, 1),
(36, 'Técnica de adquisición de datos para el Sondeo Eléctrico Vertical.', 1, 1),
(37, '¿Con qué registro geofísico se puede determinar el volumen de arcilla, límites de capas, Rw, entre otros?', 2, 1),
(38, 'A la corrección por incidencia normal de los rayos sísmicos sobre horizontes inclinados se le conoce cómo:', 3, 1),
(39, 'Los campos eléctrico y gravitacional son considerados ejemplos de campos:', 1, 1),
(40, 'Un proceso adiabático es aquel en el que un cambio en la energía interna es dado a través de un diferencial de trabajo solamente.', 1, 1),
(41, 'La deconvolución predictiva sirve para:', 2, 1),
(42, 'Dentro de las técnicas de campo gravimétricas, se debe regresar a una base seleccionada para realizar la corrección:', 2, 1),
(43, 'El campo magnético de la Tierra se puede considerar en una primera aproximación como:', 1, 1),
(44, 'La operación matemática mediante la cual se multiplica la reflectividad y una ondícula se llama:', 1, 1),
(45, 'Como técnica de campo del método magnético, lo más recomendable es utilizar una estación base para poder realizar la corrección:', 2, 1),
(46, 'De los siguientes grupos de rocas, en general ¿cuáles darían la menor respuesta magnética?', 2, 1),
(47, 'Es la propiedad física del subsuelo que se va a calcular con los Métodos Eléctricos de Exploración:', 1, 1),
(48, 'La Ley de Coulomb es la ley física que rige a los métodos eléctricos de exploración y que permite hacer los cálculos en campo en los Sondeos Eléctricos Verticales.', 1, 1),
(49, '¿Cuál de los siguientes departamentos debe tener su trabajo casi listo al empezar una prospección sísmica para evitar atrasos? ', 2, 1),
(50, 'Ecuación de Maxwell que se basa en la ley que establece que la corriente inducida en un circuito es directamente proporcional a la rapidez con que cambia el flujo magnético que lo atraviesa:', 3, 1),
(51, 'El método magnetotelúrico permite caracterizar la distribución de resistividad del subsuelo a partir de la estimación de:', 3, 1),
(52, 'En el método magnetotelúrico de fuente natural ¿qué origen tiene el campo considerado como fuente en frecuencias mayores a 1 Hz (periodos cortos)?', 3, 1),
(53, '¿Con qué registro geofísico se puede detectar la porosidad vugular?', 3, 1),
(54, 'El sondeo de resonancia magnética permite estimar la porosidad y el volumen de agua en un acuífero.', 1, 1),
(55, 'En las discontinuidades abruptas de las interfases, o estructuras cuyos radios de curvatura son más cortos que la longitud de onda incidente, ocurre una propagación radial de la energía sísmica conocida como Difracción.', 1, 1),
(56, '¿Qué comportamiento tiene la velocidad de propagación de las ondas sísmicas en el subsuelo al aumentar la profundidad?', 1, 1),
(57, 'En prospección sísmica, ¿Qué es el offset?', 1, 1),
(58, 'Las únicas características petrofísicas que determinan la calidad de una roca acumuladora de Hidrocarburos es la permeabilidad y porosidad.', 1, 1),
(59, 'La interpretación sísmica estratigráfica nos permite definir los posibles entrampamientos de hidrocarburos en un plegamiento.', 1, 1),
(60, 'La ecuación de Archie no brinda resultados correctos en formaciones areno-arcillosas ya que las lutitas adicionan conductividad a la formación.', 1, 1),
(61, 'Es un sistema de cristalización donde los tres ejes de simetría son iguales y naturalmente perpendiculares entre sí.', 1, 2),
(62, 'Es el grupo mineralógico conformado por (Mg.Fe)SiO3 m en cadenas sencillas de silicatos y que presenta dos planos de exfoliación en ángulos rectos.', 2, 2),
(63, 'Es el metal más abundante en la corteza terrestre:	', 1, 2),
(64, 'Sistema de corrientes de agua que fluyen en todas las direcciones alejándose de una estructura central elevada como un volcán:', 2, 2),
(65, 'A la Ley que dice: \"Una rápida difusión del principio y datación relativa de los estratos con sus fósiles\", se le conoce cómo:', 3, 2),
(66, 'Se produce cuando las rocas están en contacto con un cuerpo ígneo:', 2, 2),
(67, 'Fósiles de mayor importancia del grupo de los invertebrados, que tienen una gran variabilidad morfológica, ecológica y se distinguen por sus formas desprovistas de segmentación:', 3, 2),
(68, 'De acuerdo a los mecanismos físicos, las cuencas se dividen en categorías, ¿cuál de las siguientes sentencias no es una de ellas?', 3, 2),
(69, 'Es la tendencia de un mineral a romperse a lo largo de planos de enlaces débiles en su estructura cristalina.', 1, 2),
(70, 'Evento orogénico que formo la Sierra Madre Oriental', 1, 2),
(71, '¿Cómo se conoce a la extinción de los dinosaurios de acuerdo a la tabla del tiempo geológico?', 1, 2),
(72, 'Dentro de la composición química de la corteza terrestre, ¿cuál es el elemento más común?', 1, 2),
(73, '¿Cuál es el rango, en millones de años, del Periodo Cretácico de la Era Mesozoica?', 2, 2),
(74, '¿En que se basa la clasificación de las Rocas Ígneas?', 2, 2),
(75, '¿Cuál de estos minerales tiene mayor dureza?', 3, 2),
(76, 'En Geocronologia para determinar la edad de una roca a partir de las evidencias de cambios de temperatura y clima durante la historia de la tierra, se le denomina:', 2, 2),
(77, '¿Cuántas características texturales se requieren para la clasificación de Dunham de carbonatos?	', 2, 2),
(78, 'Al estudio de la distribución de la materia orgánica en la tierra y de los procesos que la controlan, se le conoce cómo:', 3, 2),
(79, '¿En qué medio ambiente de depósito se forman las evaporitas?	', 1, 2),
(80, 'La falla de San Andrés es un ejemplo de una falla', 2, 2),
(81, 'A los artrópodos exclusivamente marinos, bentónicos que tienen la posibilidad de moverse por la disposición de sus patas y que vivieron durante todo el Paleozoico, se les conoce cómo:	', 2, 2),
(82, '¿Cuál es la disciplina de la botánica que estudia polen, esporas, dinoflagelados y cualquier palinomórfo actual o fósil?', 3, 2),
(83, 'Al tipo de falla que más se relaciona a los yacimientos de gas en rocas siliciclásticas, se le conoce cómo:	', 2, 2),
(84, '¿Qué tipo de proceso tectónico da como consecuencia el acortamiento de estructuras geológicas?	', 1, 2),
(85, 'El pliegue que se forma durante la evolución de una falla lístrica se le conoce cómo:	', 2, 2),
(86, 'Los sedimentos inmaduros se acumulan en lugares tales como:', 1, 2),
(87, 'Los minerales más comunes dentro de las rocas carbonatadas son la calcita, dolomita y…', 1, 2),
(88, '¿En la actualidad porque es importante la geología de campo en la prospección petrolera?', 2, 2),
(89, 'Es la unidad fundamental de la cronoestratigrafía. Consiste en un conjunto de rocas estratificadas que se han formado durante un intervalo de tiempo determinado:', 1, 2),
(90, 'Proceso sedimentario que disgrega las rocas previamente intemperizadas, incluye mínimo movimiento de las partículas y los principales agentes que lo causan son el agua y el viento:	', 1, 2),
(91, '¿Cómo se llama la ley que define que el caudal que recorre cierta distancia es linealmente proporcional al área y al gradiente hidráulico?	', 2, 2),
(92, '¿En qué tipo de ambiente tectónico las cuencas pueden generar hidrocarburos?', 2, 2),
(93, 'Considerando los conceptos de mecánica de rocas; ¿qué materiales ante esfuerzos rápidos se rompen?', 1, 2),
(94, 'Los foraminíferos son utilizados como indicadores de:', 2, 2),
(95, 'Primer supercontinente del Proterozoico:', 1, 2),
(96, 'Los Amonites son fósiles índice característicos del:	', 2, 2),
(97, 'La Orogenia Laramide afectó a:		', 1, 2),
(98, 'Periodo en el que se extinguen cerca del 95% de las especies marinas:	', 2, 2),
(99, 'La estructura sedimentaria se define como el arreglo geométrico de las partículas que constituyen a un sedimento.', 1, 2),
(100, 'En 1793 este hombre reconoció que los fósiles se podrían utilizar para predecir la edad relativa de las rocas sedimentarias y de esta forma también se podía definir formaciones dentro de una unidad de roca:	', 3, 2),
(101, 'Proceso de cambios en el cual sedimentos no consolidados se transforman en rocas sedimentarias:			', 1, 2),
(102, 'Es una zona deprimida de la corteza terrestre de origen tectónico donde se acumulan sedimentos y para su formación se requiere un proceso de subsidencia prolongada:', 1, 2),
(103, 'Se le denomina a un tipo de falla directa, que se desarrolla y se sigue desplazando durante la sedimentación y que habitualmente posee estratos de mayor espesor en el bloque elevado deprimido que en el bloque hundido:	', 1, 2),
(104, 'Los anticlinales son doblamientos de rocas sedimentarias y/o volcánicas, inicialmente planas, en una serie de ondulaciones debido a esfuerzos compresivos.', 1, 2),
(105, 'Son fallas donde el bloque del techo se encuentra sobre el bloque del piso, el plano de falla generalmente tiene un ángulo de 30° con respecto a la horizontal:	', 1, 2),
(106, 'La Reflectancia de la Vitrinita es un método químico utilizado como termómetro para inferir la madurez térmica de las rocas generadoras de petróleo.	', 1, 2),
(107, 'La migración secundaria es un proceso que involucra interacciones complejas entre el agua de poro, el petróleo y las superficies minerales de la roca y provoca la expulsión de los hidrocarburos de la roca madre.		', 1, 2),
(108, 'En el estudio de placas tectónicas, es la capa sobre la astenósfera que incluye la corteza y parte del manto superior y alcanza hasta 100 km en espesor:	', 1, 2),
(109, 'La soldadura de sal es una superficie donde se unen estratos originalmente separados por sal.	', 2, 2),
(110, '¿Propiedad petrofísica que en las lutitas suele ser alta en?', 2, 2),
(111, 'El Flysh es un flujo de alta o baja densidad que presenta buena estratificación y/o laminación conformado por geometrías de aprones y abanicos submarinos.', 1, 2),
(112, '¿Propiedad petrofísica que en las lutitas suele ser alta en?', 2, 2),
(113, 'Es el conjunto de transformaciones químicas, físicas y biológicas del sedimento desde su deposición primaria, durante y después de su litificación por sepultamiento en el subsuelo:', 1, 2),
(114, 'Roca que contiene principalmente sedimentos de tamaño de grano entre 4 y 62 micrones de diámetro que pueden dividirse en gruesos, medios, finos y muy finos:', 3, 2),
(115, 'En el esquema de clasificación de Dott (1964), una roca clástica con 20% de matriz arcillosa, 30% de cuarzo, 20% de feldespato y 30% de líticos se clasifica como una:', 3, 2),
(116, 'Además del silicio ¿cuál es el componente fundamental de los silicatos, el grupo de minerales más abundantes en la corteza terrestre?', 1, 2),
(117, '¿Qué nombre recibe el proceso de ordenación de la estructura de átomos, iones o moléculas de un mineral?', 3, 2),
(118, 'La materia orgánica depositada en medios lacustres presenta un excelente potencial como generadora de kerógeno tipo II y posee un alto índice de hidrogeno y gran potencial en términos de cantidad de hidrocarburos ', 1, 2),
(119, 'Una Formación es un conjunto de rocas estratificadas que se diferencian de los estratos adyacentes por el predominio de una cierta litología o combinación de litologías, o por poseer rasgos litológicos unificadores o destacables.', 1, 2),
(120, 'Una Provincia Petrolera es un elemento de riesgo geológico que mide la relación cronológica existente entre la formación de la trampa y la generación-migración de los hidrocarburos en un sistema petrolero.', 1, 2),
(121, 'A la tubería que sirve como ademe, la cual es adherida a las paredes del pozo por medio de cemento, aislando el interior del pozo de las formaciones geológicas perforadas se les conoce como:', 1, 3),
(122, 'En los pozos por medio del fluido de perforación se controlan las presiones de formación y la presión hidrostática.', 1, 3),
(123, 'Sellan el Espacio anular entre tuberías del sistema de preventores y tiene concavidades que se adaptan un rango de diámetros de tuberías.', 2, 3),
(124, 'Propiedades químicas de los Fluidos de Perforación', 2, 3),
(125, 'La densidad de este aceite es mayor a los 38 grados API:', 2, 3),
(126, 'Gas que se encuentra en contacto y/o disuelto en el aceite crudo del yacimiento:', 1, 3),
(127, 'Uno de los objetivos de la toma de registros de hidrocarburos es la:', 1, 3),
(128, 'El Gasómetro es el equipo que registra los gases que provienen de la formación, separando cada uno de ellos (del C1 al IC5), que viene viajando dentro del fluido de perforación.', 1, 3),
(129, 'Las ventajas de usar lodo base aceite son: bajas perdidas de circulación y filtrado compatible con la mayoría de los fluidos de la formación.', 1, 3),
(130, 'Este patrón de flujo puede ocurrir si los hidrocarburos son producidos desde una sonda, como una FTS (Floor Temperature Sensor).', 3, 3),
(131, 'Se conectan a boca del pozo, por debajo del piso de la torre de perforación:', 1, 3),
(132, 'Los pozos que por energía propia los hidrocarburos se mueven hasta las centrales de separación, se denominan: ', 2, 3),
(133, 'Objetivos de las Tuberías de Revestimiento (4 puntos):', 2, 3),
(134, 'En el diagrama de fases (Craft and Hawkins) de un sistema multicomponente, en la segunda región entre la temperatura crítica y la cricondenterma, ¿a qué tipo de yacimientos corresponde?', 3, 3),
(135, 'Al hidrocarburo más importante que se forma durante la \"Etapa de Diagénesis\" de una cuenca sedimentaria, se le conoce cómo Aromáticos.	', 1, 3),
(136, 'A la protección de los ductos de la corrosión a través de la colocación de ánodos de magnesio, se le conoce cómo:', 3, 3),
(137, 'Una aplicación del registro DSI en la Ingeniería de Perforación es:	', 3, 3),
(138, 'Es el Gas Hidrocarburo que a condiciones de yacimiento se encuentra dentro de la fase líquida del petróleo, pero que durante su trayecto hacia la superficie cambia a fase gaseosa.', 1, 3),
(139, 'EL método Foster y Whalen se utiliza para detectar presiones anormales, el cual toma en cuenta el ritmo de penetración de la barrena, el peso sobre la barrena y la velocidad de rotación, para evaluar la compactación de los sedimentos.	', 1, 3),
(140, 'Se tiene una tubería de grado P-110, ¿cuál es el esfuerzo mínimo de cedencia de dicha tubería?	', 2, 3),
(141, '¿Qué ecuación permite calcular la presión interna de una tubería la cual describe la relación entre la presión interna, la tensión admisible, el espesor de pared y el diámetro de las tuberías?', 2, 3),
(142, 'En flujo multifásico, el Resbalamiento se usa para describir el fenómeno natural del flujo a mayor velocidad de una de las fases.', 1, 3),
(143, 'En el tipo de yacimientos Bajosaturados, se utiliza el modelo de Vogel para la determinación del comportamiento de afluencia.', 1, 3),
(144, '¿Cuáles son las geometrías de flujo o patrones de flujo que se presenta en una tubería vertical?', 2, 3),
(145, '¿Qué método se utiliza para el diseño de dimensionamiento de separadores?', 1, 3),
(146, 'Principio de operación de la tecnología de medición de placa de orificio:	', 1, 3),
(147, 'Son equipos adicionales a la infraestructura de un pozo que suministran energía adicional a los fluidos producidos por el yacimiento desde una profundidad determinada:	', 1, 3),
(148, '¿Qué término define el gasto al cual aceite o gas puede producir a través de una diferencia de presión entre el yacimiento y el pozo?	', 1, 3),
(149, 'Se define como la relación de volumen de una cantidad de gas a las condiciones de yacimiento al volumen de la misma cantidad a las condiciones estándar.', 2, 3),
(150, 'En el sistema artificial de producción por cavidades progresivas, ¿cuál es el nombre del elemento en forma de doble hélice, el cual se encuentra fijo y es una camisa de acero revestida internamente en caucho moldeado?', 3, 3),
(151, 'Es gas dentro de un yacimiento, que no contiene grandes cantidades de aceite coexistiendo con él. Es decir, a condiciones iniciales de yacimiento ya se encontraba en fase gaseosa.', 1, 3),
(152, 'Mencione los tres componentes aromáticos más comunes presentes en el petróleo crudo:', 2, 3),
(153, '¿Cuál es el nombre de los autores que introdujeron el método de imágenes para determinar la presión promedio de un yacimiento limitado?	', 3, 3),
(154, 'Este efecto establece que la permeabilidad de los gases a bajas presiones puede ser mayor que la permeabilidad absoluta:', 1, 3),
(155, '¿Cómo se define matemáticamente el término de capacidad de flujo?', 1, 3),
(156, 'Es la representación gráfica en coordenadas log-log de una familia de curvas de presión y/o función derivada, que muestran el comportamiento típico del sistema ante un modelo de pozo, yacimiento y frontera:', 3, 3),
(157, 'El desarrollo de esta ecuación se atribuye a Leverett y para deducirla, se considera un desplazamiento tipo pistón con fugas, en el cual el fluido desplazado es el petróleo y el desplazante es el agua:', 3, 3),
(158, '¿Cuál sería el valor del radio del pozo denotado por Rw al resolver la ecuación de difusividad para la solución línea fuente?', 2, 3),
(159, 'Este modelo de entrada de agua se basa en la ecuación radial de difusión y utiliza superposición para calcular la entrada de agua:', 2, 3),
(160, 'Para el cálculo de la trayectoria direccional, este método es el más exacto de todos, utiliza la inclinación y la dirección del pozo medido en los extremos superior e inferior de la longitud del trayecto para generar un arco suave que representa la trayectoria del pozo:', 3, 3),
(161, 'Estos yacimientos contienen principalmente metano (%C1 > 90) con pequeñas cantidades de otros componentes más pesados. Dado su alto contenido de componentes volátiles del gas seco, la condensación del líquido solo se alcanza a temperaturas bajo 0°F.', 1, 3),
(162, 'En que tipo de yacimientos la presión de éste es mayor a la presión de saturación:', 2, 3),
(163, 'Se define como el cociente del volumen acumulativo de aceite producido @cs a una fecha determinada (Np) entre el volumen original de aceite @cs (N):', 3, 3),
(164, 'Son utilizados para tener una adecuada clasificación de los yacimientos, tomando en cuenta la composición de la mezcla de hidrocarburos, la temperatura y la presión:', 2, 3),
(165, 'Al valor de saturación a partir del cual el fluido correspondiente (agua, gas o aceite) puede empezar a moverse, se le denomina:', 1, 3),
(166, '¿Cómo se le llama a la saturación mínima de gas o aceite que queda en un yacimiento después de etapas avanzadas de explotación?	', 1, 3),
(167, 'Es una medida del cambio de volumen del fluido con la presión, considerando un volumen (V) dado:', 1, 3),
(168, '¿Cuál es el método probabilístico más utilizado para calcular la cantidad de petróleo que se estima existe originalmente en el yacimiento?', 2, 3),
(169, 'Conocer el volumen original de aceite y tener una clara idea del movimiento de los fluidos dentro del yacimiento, es una de las funciones de la Simulación Numerica de Yacimientos, ¿Cierto o Falso?', 1, 3),
(170, 'Se definen como la pérdida total o parcial del fluido de control (lodo de perforación) hacia una formación muy permeable, en la superficie se detecta observando el nivel de las presas de lodo:', 1, 3),
(171, 'Proceso en donde una lechada de cemento no contaminante es desplazada a un área específica del pozo, detrás de la tubería de revestimiento o de la formación a una profundidad dada evitando la migración vertical de los fluidos indeseables:', 2, 3),
(172, '¿En dónde se utilizan los apartaflamas y las válvulas de presión y vacío en las instalaciones superficiales de producción?', 2, 3),
(173, '¿Cómo se llama al proceso operativo que se inicia después de cementada la última tubería de revestimiento de explotación y se realiza con el fin de dejar al pozo produciendo hidrocarburos?', 1, 3),
(174, '¿Cómo se denomina al lugar geométrico de los puntos Presión-Temperatura para los cuales se forma la primera gota de líquido al pasar de la región de vapor a la región de dos fases?', 2, 3),
(175, '¿Cómo se le conoce al estado de presión y temperatura para la cual las propiedades intensivas (viscosidad, densidad, etc.) de las fases líquida y gaseosa son idénticas?', 2, 3),
(176, '¿Cómo se le llama al mecanismo de desplazamiento donde el aceite, gas y agua tienden a distribuirse en el yacimiento de acuerdo a sus densidades?', 2, 3),
(177, 'La flotabilidad, la tensión superficial y el régimen hidrodinámico son fuerzas que intervienen en la migración secundaria de los hidrocarburos.', 1, 3),
(178, 'Las Resinas son compuestos de alto peso molecular complejos que no son solubles en acetato de etilo, pero son solubles en n-heptano los cuales contienen hetero átomos de oxígeno, nitrógeno y átomos de azufre.', 1, 3),
(179, 'Campo de hidrocarburos que tiene propiedades similares y una etapa más avanzada de desarrollo o producción respecto al prospecto o campo de interés, se le conoce como campo análogo.', 1, 3),
(180, 'Saturación de agua en un yacimiento retenida alrededor de los granos de roca por tensión superficial que no puede ser desplazada por la migración de hidrocarburos, se le conoce como Saturación de Agua Connata.', 1, 3),
(181, 'A la técnica de adquisición de datos adquiridos con receptores de más de una componente y que se repite en un lapso de tiempo, se le conoce como:', 2, 4),
(182, 'Durante la planeación de una prospección sísmica, ¿qué método geofísico nos proporciona información del tamaño del paquete sedimentario?', 1, 4),
(183, 'La Porosidad, Permeabilidad, Saturación, Fuerzas capilares y Resistividad son propiedades petrofísicas más importantes de las rocas', 1, 4),
(184, 'Es el estado de equilibrio gravitatorio y bajo el cual se regula la altura de los continentes y los fondos oceánicos.', 2, 4),
(185, '¿Con qué registros geofísicos se puede determinar la dirección del esfuerzo mínimo horizontal en un pozo?', 3, 4),
(186, 'La discontinuidad de Gutenberg la encontramos entre:', 1, 4),
(187, '¿Cuáles son los principales biopolímeros que se encuentran en la materia orgánica?', 2, 4),
(188, 'Como resultado de la maduración de la materia orgánica sedimentaria, se obtiene un geopolímero que se describe como materia orgánica insoluble en solventes orgánicos y es el precursor de los hidrocarburos:', 2, 4),
(189, '¿Quién(es) propone(n) una clasificación de los hidrocarburos basados en las proporciones relativas de los alcanos, ciclo-alcanos y compuestos aromáticos de N, S y O?', 3, 4),
(190, '¿Cuáles son los registros geofísicos de pozos que se toman al momento de que se perfora un pozo?', 1, 4),
(191, 'El potencial espontaneo se debe principalmente a:', 2, 4),
(192, 'Un checkshot es una tabla donde se puede observar tiempos y profundidades', 1, 4),
(193, '¿Cuáles de las siguientes cuencas no es productora de gas?', 2, 4),
(194, 'Las funciones principales de los fluidos de Perforación es la remoción de los recortes del pozo y el control de las presiones de la formación', 1, 4),
(195, '¿Qué método geofísico es el más utilizado para la delimitación de un yacimiento?', 1, 4),
(196, 'Es la zona de la litosfera constituida mayoritariamente por rocas básicas (silicatos de magnesio y hierro) con espesores de 35 a 40 km.', 2, 4),
(197, 'La información acerca de la estructura del interior de la Tierra es obtenida de:', 1, 4),
(198, '¿A qué época geológica pertenecen las formaciones Méndez, San Felipe y Agua Nueva?', 2, 4),
(199, 'La porosidad efectiva es la suma del volumen de poros no interconectados más los poros interconectados entre el volumen total de la roca.', 1, 4),
(200, 'El \"Play\" es un término que define:', 3, 4),
(201, 'Las características de los \"Play`s\", pueden ser: físicas, genéticas y:', 2, 4),
(202, 'Al kerógeno que se forma en condiciones moderadas de temperatura y presión de los sedimentos jóvenes el cual contiene muchas cadenas alifáticas y pocos núcleos aromáticos, se le denomina:', 3, 4),
(203, '¿Con qué registro geofísico se puede detectar la porosidad vugular?', 2, 4),
(204, 'El Moho es el límite entre:', 1, 4),
(205, 'La capa de cambio rápido de salinidad en el océano es conocida como:', 3, 4),
(206, '¿Para qué sirve un sismograma sintético?', 1, 4),
(207, 'Las Reservas No Desarrolladas son aquellas acumulaciones que se espera, serán recuperadas de los intervalos terminados que están abiertos y en producción en el momento de hacer la estimación.', 1, 4),
(208, '¿En qué Cuenca de México se encuentra el yacimiento conocido como Faja de Oro?', 2, 4),
(209, '¿Cuáles son los componentes principales de los hidrocarburos?', 1, 4),
(210, '¿Qué capa del interior de la Tierra es líquida, por lo que las ondas de Cizalla no se transmiten por él?', 1, 4),
(211, '¿Qué discontinuidad se sitúa debajo de la corteza oceánica?', 1, 4),
(212, '¿Cuál es el factor físico más importante para la generación de los hidrocarburos?', 2, 4),
(213, 'Es una roca ígnea extrusiva con un contenido mayor de 63% de SiO2:', 2, 4),
(214, 'Las características geológicas del subsuelo como son el tipo de roca, facie sedimentaria y tamaño de garganta de poro, representan:', 2, 4),
(215, '¿Cuál de los siguientes agentes tiene un menor poder erosivo sobre la superficie de terrestre?', 1, 4),
(216, 'La Permeabilidad es una característica petrofísica de las rocas de los Yacimientos, que se define como la capacidad que tiene una roca de permitir el flujo de fluidos a través de sus poros interconectados.', 1, 4),
(217, 'La presencia de Hidrocarburo en la superficie se puede clasificar por dos tipos de manifestaciones:', 1, 4),
(218, 'A las filtraciones de petróleo o asfalto líquido a través de fractura, fallas o discordancia y llegan a la superficie, se le conoce como:', 1, 4),
(219, 'Los Registros geofísicos que se toman después de que se ha cementado una tubería de revestimiento, son:', 2, 4),
(220, 'Un ambiente de depósito es aquel en el cual los componentes físicos, químicos y biológicos externos son los que interactúan con los depósitos', 1, 4),
(221, 'Estos compuestos sólidos asemejan hielo sucio los cuales son causados por la reacción de gas natural con agua, los cuales consisten en aproximadamente 10 % de hidrocarburos y 90 % de agua:', 1, 4),
(222, 'Esta unidad se define como un centipoise dividido por la densidad en gm/cm3, cuyas unidades son cm2/100seg:', 2, 4),
(223, 'Propiedad que tienen las rocas de permitir el paso de fluidos y/ o gases a través de ellas:	', 1, 4),
(224, '¿Qué es una discordancia?', 1, 4),
(225, 'Conjunto de elementos y eventos geológicos que coadyuvan en el tiempo y espacio de una cuenca para formar acumulaciones de hidrocarburos:', 2, 4),
(226, 'Procedimiento de simulación y análisis estadístico que incorpora parámetros de incertidumbre para evaluar volumetría y riesgo y jerarquizar los prospectos exploratorios:', 2, 4),
(227, 'Es la presión actuando en los fluidos intersticiales de un volumen de roca:', 2, 4),
(228, 'Representación tridimensional de un yacimiento con sus propiedades petrofísicas.', 1, 4),
(229, 'Acción de acondicionar todos los elementos que componen un yacimiento para representar un sentido geológico en el mismo', 1, 4),
(230, 'Al hidrocarburo más importante que se forma durante la \"Etapa de Diagénisis\" de una cuenca sedimentaria, se le conoce cómo:', 3, 4),
(231, 'Uno de los resultados finales principales en la caracterización estática es el cálculo de volumen original', 1, 4),
(232, '¿Cuáles son las características petrofísicas fundamentales que determinan la calidad de una roca acumuladora de hidrocarburos?', 2, 4),
(233, '¿Qué disciplina geológica permite tener información de la génesis, acumulación y entrampamiento de los hidrocarburos?', 2, 4),
(234, '¿Qué factores son los más influyentes en la creación de las rocas metamórficas y magmáticas?', 2, 4),
(235, '¿Cuál de las siguientes propiedades de los minerales no hacen referencia a factores físicos?', 1, 4),
(236, 'Los elementos del sistema petrolero en un yacimiento convencional son: la roca madre, la roca sello y la roca almacen.', 1, 4),
(237, 'El tipo de muestra continua que permite la identificación litológica en función del avance de la perforación es:', 1, 4),
(238, '¿Qué nombre reciben los depósitos orgánicos procedentes de restos vegetales descompuestos por la falta de oxígeno?', 2, 4),
(239, '¿Cuál de los siguientes tipos de carbón no es en realidad una roca sedimentaria, sino metamórfica?', 2, 4),
(240, '¿Cuál es el equipo que registra los gases que provienen de la formación, separando cada uno de ellos (del C1 al IC5) que viene viajando dentro del fluido de perforación?', 3, 4),
(241, 'La Luna gira alrededor de la Tierra llevando a cabo una revolución completa en 27 días. Si consideramos que la órbita que describe la Luna alrededor de nuestro planeta es circular y con un radio de 385 000 km ¿cuál es la magnitud de la aceleración centrípeta de la Luna hacia la Tierra?', 3, 4),
(242, '¿Cuál considera que es la principal diferencia entre un levantamiento sísmico 2D y un 3D?', 2, 4),
(243, 'A la porción de petróleo que existe en los yacimientos en fase semisólida o sólida y que en su estado natural generalmente contiene azufre, metales y otros compuestos que no son hidrocarburos, se le conoce como:', 3, 4),
(244, '¿Qué disciplina trata la edad absoluta o relativa y las relaciones temporales de los cuerpos rocosos?', 1, 4),
(245, 'Dentro de la clasificación de las rocas carbonatadas se incluyen dos grandes tipos de partículas, estas son:', 3, 4),
(246, 'Es el estudio de la deformación tectónica que involucra el flujo lateral y vertical de halita y otras evaporitas y su desplazamiento diapírico a través de la cubierta sedimentaria:', 2, 4),
(247, '¿Qué registro geofísico permite conocer la geometría del pozo?', 2, 4),
(248, 'Son aquellas reservas no probadas para las cuales el análisis de la información geológica y de ingeniería del yacimiento sugiere que son más factibles de ser comercialmente recuperables:', 2, 4),
(249, 'Son aquellos volúmenes de hidrocarburos cuya información geológica y de ingeniería sugiere que es menos factible su recuperación comercial:', 2, 4),
(250, '¿En qué mapas se muestran las características de las rocas y su variación vertical y horizontal dentro de la formación?', 2, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas_generadas`
--

CREATE TABLE `preguntas_generadas` (
  `ID_GENERADA` int(11) NOT NULL,
  `ID_PREGUNTA` int(6) NOT NULL,
  `ID_CONCURSO` int(6) NOT NULL,
  `PREGUNTA_POSICION` int(3) DEFAULT '0',
  `ID_RONDA` int(2) DEFAULT '0',
  `LANZADA` tinyint(1) DEFAULT '0',
  `HECHA` tinyint(1) UNSIGNED DEFAULT '0',
  `NIVEL_EMPATE` int(2) DEFAULT '0',
  `ID_CONCURSANTE` int(6) DEFAULT NULL,
  `OLEADA` smallint(3) DEFAULT '0',
  `TIEMPO_TRANSCURRIDO` int(3) DEFAULT '0',
  `TIEMPO_TRANSCURRIDO_PASO` int(3) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `preguntas_generadas`
--

INSERT INTO `preguntas_generadas` (`ID_GENERADA`, `ID_PREGUNTA`, `ID_CONCURSO`, `PREGUNTA_POSICION`, `ID_RONDA`, `LANZADA`, `HECHA`, `NIVEL_EMPATE`, `ID_CONCURSANTE`, `OLEADA`, `TIEMPO_TRANSCURRIDO`, `TIEMPO_TRANSCURRIDO_PASO`) VALUES
(1, 33, 1, 1, 1, 0, 0, 0, NULL, 0, 0, 0),
(2, 54, 1, 2, 1, 0, 0, 0, NULL, 0, 0, 0),
(3, 24, 1, 3, 1, 0, 0, 0, NULL, 0, 0, 0),
(4, 50, 1, 4, 1, 0, 0, 0, NULL, 0, 0, 0),
(5, 59, 1, 1, 2, 0, 0, 0, NULL, 0, 0, 0),
(6, 16, 1, 2, 2, 0, 0, 0, NULL, 0, 0, 0),
(7, 29, 1, 3, 2, 0, 0, 0, NULL, 0, 0, 0),
(8, 6, 1, 4, 2, 0, 0, 0, NULL, 0, 0, 0),
(9, 60, 2, 1, 1, 1, 1, 0, NULL, 0, 0, 0),
(10, 25, 2, 2, 1, 0, 0, 0, NULL, 0, 0, 0),
(11, 28, 2, 3, 1, 0, 0, 0, NULL, 0, 0, 0),
(12, 50, 2, 4, 1, 0, 0, 0, NULL, 0, 0, 0),
(13, 20, 2, 1, 2, 0, 0, 0, NULL, 0, 0, 0),
(14, 19, 2, 2, 2, 0, 0, 0, NULL, 0, 0, 0),
(15, 3, 2, 3, 2, 0, 0, 0, NULL, 0, 0, 0),
(16, 2, 2, 4, 2, 0, 0, 0, NULL, 0, 0, 0),
(17, 13, 3, 1, 1, 0, 0, 0, NULL, 0, 0, 0),
(18, 60, 3, 2, 1, 0, 0, 0, NULL, 0, 0, 0),
(19, 45, 3, 3, 1, 0, 0, 0, NULL, 0, 0, 0),
(20, 18, 3, 4, 1, 0, 0, 0, NULL, 0, 0, 0),
(21, 57, 3, 1, 2, 0, 0, 0, NULL, 0, 0, 0),
(22, 43, 3, 2, 2, 0, 0, 0, NULL, 0, 0, 0),
(23, 46, 3, 3, 2, 0, 0, 0, NULL, 0, 0, 0),
(24, 50, 3, 4, 2, 0, 0, 0, NULL, 0, 0, 0),
(25, 34, 4, 1, 1, 1, 1, 0, NULL, 0, 0, 0),
(26, 26, 4, 2, 1, 2, 1, 0, NULL, 0, 0, 0),
(27, 49, 4, 3, 1, 3, 1, 0, NULL, 0, 0, 0),
(28, 52, 4, 4, 1, 4, 1, 0, NULL, 0, 0, 0),
(29, 44, 4, 1, 2, 1, 1, 0, NULL, 0, 0, 0),
(30, 36, 4, 2, 2, 2, 1, 0, NULL, 0, 0, 0),
(31, 4, 4, 3, 2, 3, 1, 0, NULL, 0, 0, 0),
(32, 38, 4, 4, 2, 4, 1, 0, NULL, 0, 0, 0),
(33, 139, 5, 1, 1, 0, 0, 0, NULL, 0, 0, 0),
(34, 178, 5, 2, 1, 0, 0, 0, NULL, 0, 0, 0),
(35, 149, 5, 3, 1, 0, 0, 0, NULL, 0, 0, 0),
(36, 156, 5, 4, 1, 0, 0, 0, NULL, 0, 0, 0),
(37, 147, 5, 1, 2, 0, 0, 0, NULL, 0, 0, 0),
(38, 166, 5, 2, 2, 0, 0, 0, NULL, 0, 0, 0),
(39, 158, 5, 3, 2, 0, 0, 0, NULL, 0, 0, 0),
(40, 150, 5, 4, 2, 0, 0, 0, NULL, 0, 0, 0),
(41, 170, 6, 1, 1, 0, 0, 0, NULL, 0, 0, 0),
(42, 177, 6, 2, 1, 0, 0, 0, NULL, 0, 0, 0),
(43, 132, 6, 3, 1, 0, 0, 0, NULL, 0, 0, 0),
(44, 130, 6, 4, 1, 0, 0, 0, NULL, 0, 0, 0),
(45, 161, 6, 1, 2, 0, 0, 0, NULL, 0, 0, 0),
(46, 178, 6, 2, 2, 0, 0, 0, NULL, 0, 0, 0),
(47, 141, 6, 3, 2, 0, 0, 0, NULL, 0, 0, 0),
(48, 137, 6, 4, 2, 0, 0, 0, NULL, 0, 0, 0),
(49, 126, 7, 1, 1, 0, 0, 0, NULL, 0, 0, 0),
(50, 167, 7, 2, 1, 0, 0, 0, NULL, 0, 0, 0),
(51, 141, 7, 3, 1, 0, 0, 0, NULL, 0, 0, 0),
(52, 163, 7, 4, 1, 0, 0, 0, NULL, 0, 0, 0),
(53, 127, 7, 1, 2, 0, 0, 0, NULL, 0, 0, 0),
(54, 128, 7, 2, 2, 0, 0, 0, NULL, 0, 0, 0),
(55, 123, 7, 3, 2, 0, 0, 0, NULL, 0, 0, 0),
(56, 157, 7, 4, 2, 0, 0, 0, NULL, 0, 0, 0),
(57, 146, 8, 1, 1, 1, 1, 0, NULL, 0, 0, 0),
(58, 148, 8, 2, 1, 2, 1, 0, NULL, 0, 0, 0),
(59, 132, 8, 3, 1, 3, 1, 0, NULL, 0, 0, 0),
(60, 150, 8, 4, 1, 4, 1, 0, NULL, 0, 0, 0),
(61, 167, 8, 1, 2, 1, 1, 0, NULL, 0, 0, 0),
(62, 177, 8, 2, 2, 2, 1, 0, NULL, 0, 0, 0),
(63, 172, 8, 3, 2, 3, 1, 0, NULL, 0, 0, 0),
(64, 134, 8, 4, 2, 4, 1, 0, NULL, 0, 0, 0),
(65, 19, 9, 1, 1, 1, 1, 0, NULL, 0, 0, 0),
(66, 17, 9, 2, 1, 2, 1, 0, NULL, 0, 0, 0),
(67, 29, 9, 3, 1, 3, 1, 0, NULL, 0, 0, 0),
(68, 31, 9, 4, 1, 4, 1, 0, NULL, 0, 0, 0),
(69, 60, 9, 1, 2, 1, 1, 0, NULL, 0, 0, 0),
(70, 54, 9, 2, 2, 2, 1, 0, NULL, 0, 0, 0),
(71, 21, 9, 3, 2, 3, 1, 0, NULL, 0, 0, 0),
(72, 6, 9, 4, 2, 4, 1, 0, NULL, 0, 0, 0),
(73, 103, 10, 1, 1, 1, 1, 0, NULL, 0, 0, 0),
(74, 71, 10, 2, 1, 2, 1, 0, NULL, 0, 0, 0),
(75, 76, 10, 3, 1, 3, 1, 0, NULL, 0, 0, 0),
(76, 115, 10, 4, 1, 4, 1, 0, NULL, 0, 0, 0),
(77, 86, 10, 1, 2, 1, 1, 0, NULL, 0, 0, 0),
(78, 101, 10, 2, 2, 2, 1, 0, NULL, 0, 0, 0),
(79, 74, 10, 3, 2, 3, 1, 0, NULL, 0, 0, 0),
(80, 117, 10, 4, 2, 4, 1, 0, NULL, 0, 0, 0),
(81, 83, 10, 1, 3, 1, 1, 1, NULL, 0, 0, 0),
(82, 62, 10, 2, 3, 2, 1, 1, NULL, 0, 0, 0),
(83, 109, 10, 3, 3, 3, 1, 1, NULL, 0, 0, 0),
(84, 114, 10, 4, 3, 4, 1, 1, NULL, 0, 0, 0),
(85, 85, 10, 5, 3, 1, 1, 2, NULL, 0, 0, 0),
(86, 98, 10, 6, 3, 2, 1, 2, NULL, 0, 0, 0),
(87, 66, 10, 7, 3, 3, 1, 2, NULL, 0, 0, 0),
(88, 65, 10, 8, 3, 4, 1, 2, NULL, 0, 0, 0),
(89, 19, 11, 1, 1, 0, 0, 0, NULL, 0, 0, 0),
(90, 39, 11, 2, 1, 0, 0, 0, NULL, 0, 0, 0),
(91, 35, 11, 3, 1, 0, 0, 0, NULL, 0, 0, 0),
(92, 31, 11, 4, 1, 0, 0, 0, NULL, 0, 0, 0),
(93, 55, 11, 1, 2, 0, 0, 0, NULL, 0, 0, 0),
(94, 58, 11, 2, 2, 0, 0, 0, NULL, 0, 0, 0),
(95, 32, 11, 3, 2, 0, 0, 0, NULL, 0, 0, 0),
(96, 30, 11, 4, 2, 0, 0, 0, NULL, 0, 0, 0),
(97, 23, 12, 1, 1, 1, 1, 0, NULL, 0, 0, 0),
(98, 25, 12, 2, 1, 2, 1, 0, NULL, 0, 0, 0),
(99, 15, 12, 3, 1, 3, 1, 0, NULL, 0, 0, 0),
(100, 53, 12, 4, 1, 4, 1, 0, NULL, 0, 0, 0),
(101, 48, 12, 1, 2, 1, 1, 0, NULL, 0, 0, 0),
(102, 36, 12, 2, 2, 2, 1, 0, NULL, 0, 0, 0),
(103, 22, 12, 3, 2, 3, 1, 0, NULL, 0, 0, 0),
(104, 31, 12, 4, 2, 4, 1, 0, NULL, 0, 0, 0),
(105, 167, 13, 1, 1, 1, 1, 0, NULL, 0, 0, 0),
(106, 146, 13, 2, 1, 2, 1, 0, NULL, 0, 0, 0),
(107, 141, 13, 3, 1, 3, 1, 0, NULL, 0, 0, 0),
(108, 153, 13, 4, 1, 4, 1, 0, NULL, 0, 0, 0),
(109, 143, 13, 1, 2, 1, 1, 0, NULL, 0, 0, 0),
(110, 161, 13, 2, 2, 2, 1, 0, NULL, 0, 0, 0),
(111, 159, 13, 3, 2, 3, 1, 0, NULL, 0, 0, 0),
(112, 130, 13, 4, 2, 4, 1, 0, NULL, 0, 0, 0),
(113, 149, 13, 1, 3, 1, 1, 1, NULL, 0, 0, 0),
(114, 172, 13, 2, 3, 2, 1, 1, NULL, 0, 0, 0),
(115, 174, 13, 3, 3, 3, 1, 1, NULL, 0, 0, 0),
(116, 160, 13, 4, 3, 4, 1, 1, NULL, 0, 0, 0),
(117, 209, 14, 1, 4, 1, 1, 0, NULL, 0, 0, 0),
(118, 183, 14, 2, 4, 2, 1, 0, NULL, 0, 0, 0),
(119, 238, 14, 3, 4, 3, 1, 0, NULL, 0, 0, 0),
(120, 230, 14, 4, 4, 4, 1, 0, NULL, 0, 0, 0),
(121, 236, 14, 1, 5, 1, 1, 0, 29, 1, 9, 4),
(122, 199, 14, 2, 5, 2, 1, 0, 29, 2, 5, 3),
(123, 248, 14, 3, 5, 3, 1, 0, 29, 3, 6, 0),
(124, 205, 14, 4, 5, 4, 1, 0, 29, 4, 8, 3),
(125, 228, 14, 5, 5, 1, 1, 0, 30, 1, 4, 0),
(126, 237, 14, 6, 5, 2, 1, 0, 30, 2, 4, 3),
(127, 188, 14, 7, 5, 3, 1, 0, 30, 3, 2, 0),
(128, 202, 14, 8, 5, 4, 1, 0, 30, 4, 5, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reglas`
--

CREATE TABLE `reglas` (
  `ID_REGLA` int(11) NOT NULL,
  `ID_RONDA` int(11) NOT NULL,
  `TIENE_PASO` tinyint(1) DEFAULT '0',
  `TIENE_TURNOS` tinyint(1) DEFAULT '0',
  `RESTA_PASO` tinyint(1) DEFAULT '0',
  `RESTA_ERROR` tinyint(1) DEFAULT '0',
  `GRADOS` varchar(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `reglas`
--

INSERT INTO `reglas` (`ID_REGLA`, `ID_RONDA`, `TIENE_PASO`, `TIENE_TURNOS`, `RESTA_PASO`, `RESTA_ERROR`, `GRADOS`) VALUES
(1, 1, 0, 0, 0, 0, '1,1,2,3'),
(2, 2, 0, 0, 0, 1, '1,1,2,3'),
(3, 3, 0, 0, 0, 1, '2,2,2,3'),
(4, 4, 0, 0, 0, 0, '1,1,2,3'),
(5, 5, 1, 1, 1, 1, '1,1,2,3'),
(6, 6, 0, 0, 0, 1, '1,1,2,3');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuestas`
--

CREATE TABLE `respuestas` (
  `ID_RESPUESTA` int(11) NOT NULL,
  `ID_PREGUNTA` int(6) NOT NULL,
  `INCISO` varchar(8) NOT NULL,
  `RESPUESTA` varchar(2048) DEFAULT '',
  `ES_CORRECTA` tinyint(1) DEFAULT NULL,
  `ES_IMAGEN` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `respuestas`
--

INSERT INTO `respuestas` (`ID_RESPUESTA`, `ID_PREGUNTA`, `INCISO`, `RESPUESTA`, `ES_CORRECTA`, `ES_IMAGEN`) VALUES
(38, 1, 'A', 'El tamaño del bin', 1, 0),
(39, 1, 'B', 'El offset máximo', 0, 0),
(40, 1, 'C', 'El intervalo entre líneas receptoras', 0, 0),
(41, 1, 'D', 'El fold', 0, 0),
(42, 2, 'A', 'La grafica de la fase contra la frecuencia', 0, 0),
(43, 2, 'B', 'La grafica de la densidad de amplitud contra la potencia', 0, 0),
(44, 2, 'C', 'La grafica de la densidad de amplitud contra la frecuencia', 1, 0),
(45, 2, 'D', 'La grafica de la potencia contra la fase', 0, 0),
(46, 3, 'A', 'Transito vehicular', 0, 0),
(47, 3, 'B', 'Presencia de grandes masas', 0, 0),
(48, 3, 'C', 'Corrientes de aire', 0, 0),
(49, 3, 'D', 'Postes de electricidad', 1, 0),
(50, 4, 'A', 'Transito vehicular', 1, 0),
(51, 4, 'B', 'Presencia de grandes masas', 0, 0),
(52, 4, 'C', 'Corrientes de aire', 0, 0),
(53, 4, 'D', 'Postes de electricidad', 0, 0),
(54, 5, 'A', 'Barrido sísmico', 1, 0),
(55, 5, 'B', 'Impulso elástico', 0, 0),
(56, 5, 'C', 'Impulso sísmico', 0, 0),
(57, 5, 'D', 'Onda sísmica', 0, 0),
(58, 6, 'A', 'Ondícula de Rayleigh', 0, 0),
(59, 6, 'B', 'Ondícula de Ricker', 0, 0),
(60, 6, 'C', 'Ondícula de Klauder', 1, 0),
(61, 6, 'D', 'Ondícula Madre', 0, 0),
(62, 7, 'A', 'Análisis de Ruido Residual', 0, 0),
(63, 7, 'B', 'Prueba Direccional', 0, 0),
(64, 7, 'C', 'Análisis de Ruido', 1, 0),
(65, 7, 'D', 'Prueba de Patrones de Detección', 0, 0),
(66, 8, 'A', 'Ohm - metro', 0, 0),
(67, 8, 'B', 'Gammas o nanoteslas', 1, 0),
(68, 8, 'C', 'Metros por segundo', 0, 0),
(69, 8, 'D', 'Gales o Miligales', 0, 0),
(70, 9, 'A', '99 a 1000', 0, 0),
(71, 9, 'B', '10 a 100', 0, 0),
(72, 9, 'C', '10,000 a 100,000', 0, 0),
(73, 9, 'D', '1,000 a 10,000', 1, 0),
(74, 10, 'A', 'Densidad y velocidad', 0, 0),
(75, 10, 'B', 'Velocidad y susceptibilidad', 0, 0),
(76, 10, 'C', 'Porosidad y permeabilidad', 0, 0),
(77, 10, 'D', 'Densidad y susceptibilidad', 1, 0),
(78, 11, 'A', 'Geoide	', 1, 0),
(79, 11, 'B', 'Geopotencial', 0, 0),
(80, 11, 'C', 'Elipsoide', 0, 0),
(81, 11, 'D', 'Geosfera', 0, 0),
(82, 12, 'A', 'En los polos.', 1, 0),
(83, 12, 'B', 'En las montañas altas.', 0, 0),
(84, 12, 'C', 'En el Ecuador.', 0, 0),
(85, 12, 'D', 'En las fosas oceánicas', 0, 0),
(86, 13, 'A', 'Módulo de Young', 1, 0),
(87, 13, 'B', 'Módulo de Poisson', 0, 0),
(88, 13, 'C', 'Módulo de Compresibilidad', 0, 0),
(89, 13, 'D', 'Módulo volumétrico', 0, 0),
(90, 14, 'A', 'El efecto de fatiga del resorte.', 0, 0),
(91, 14, 'B', 'El efecto de los cometas.', 0, 0),
(92, 14, 'C', 'El efecto Lunisolar.', 1, 0),
(93, 14, 'D', 'Las tormentas solares', 0, 0),
(94, 15, 'A', 'Velocidad NMO', 1, 0),
(95, 15, 'B', 'Velocidad RMS', 0, 0),
(96, 15, 'C', 'Velocidad Onda P', 0, 0),
(97, 15, 'D', 'Velocidad Media', 0, 0),
(98, 16, 'A', 'Punto de Curie', 1, 0),
(99, 16, 'B', 'Punto de Fusión', 0, 0),
(100, 16, 'C', 'Punto de paramagnetización', 0, 0),
(101, 16, 'D', 'Ninguna de las anteriores', 0, 0),
(102, 17, 'A', 'En la mesósfera', 0, 0),
(103, 17, 'B', 'En la litósfera', 0, 0),
(104, 17, 'C', 'En el núcleo interno', 0, 0),
(105, 17, 'D', 'En el núcleo externo', 1, 0),
(106, 18, 'A', 'Bouguer', 0, 0),
(107, 18, 'B', 'Aire libre Positivas', 0, 0),
(108, 18, 'C', 'Bouguer Negativas', 1, 0),
(109, 18, 'D', 'Aire libre', 0, 0),
(110, 19, 'A', 'FALSO', 0, 0),
(111, 19, 'B', 'VERDADERO', 1, 0),
(112, 20, 'A', 'FALSO', 0, 0),
(113, 20, 'B', 'VERDADERO', 1, 0),
(114, 21, 'A', '10^2', 0, 0),
(115, 21, 'B', '10^3', 0, 0),
(116, 21, 'C', '10^4', 1, 0),
(117, 21, 'D', '10^5', 0, 0),
(118, 22, 'A', 'La suma de sus espectros de amplitud.', 0, 0),
(119, 22, 'B', 'La multiplicación de sus espectros de amplitud.', 1, 0),
(120, 23, 'A', 'FALSO', 1, 0),
(121, 23, 'B', 'VERDADERO', 0, 0),
(122, 24, 'A', 'Nyquist', 1, 0),
(123, 24, 'B', 'Aliasing', 0, 0),
(124, 24, 'C', 'Gibss', 0, 0),
(125, 24, 'D', 'Pasa bajas', 0, 0),
(126, 25, 'A', 'Coeficiente de reflexión', 0, 0),
(127, 25, 'B', 'Serie Reflectiva', 0, 0),
(128, 25, 'C', 'Coeficiente de transmisión', 0, 0),
(129, 25, 'D', 'Impedancia acústica', 1, 0),
(130, 26, 'A', 'Compresional', 0, 0),
(131, 26, 'B', 'Transversal', 0, 0),
(132, 26, 'C', 'Superficial “Rayleigh”	', 0, 0),
(133, 26, 'D', 'Superficial “Love”', 1, 0),
(134, 27, 'A', 'de Snell', 0, 0),
(135, 27, 'B', 'de Huyghens', 1, 0),
(136, 27, 'C', 'de Fermat', 0, 0),
(137, 27, 'D', 'de la Elasticidad', 0, 0),
(138, 28, 'A', 'Ondas múltiples', 1, 0),
(139, 28, 'B', 'Onda de Aire', 0, 0),
(140, 28, 'C', 'Ondas superficiales	', 0, 0),
(141, 28, 'D', 'Ondas laterales', 0, 0),
(142, 29, 'A', 'Convolución.', 1, 0),
(143, 29, 'B', 'Correlación.', 0, 0),
(144, 29, 'C', 'Deconvolución.', 0, 0),
(145, 29, 'D', 'Autocorrelación', 0, 0),
(146, 30, 'A', 'La grafica de la fase contra la frecuencia', 0, 0),
(147, 30, 'B', 'La grafica de la densidad de amplitud contra la potencia', 0, 0),
(148, 30, 'C', 'La grafica de la densidad de amplitud contra la frecuencia', 0, 0),
(149, 30, 'D', 'La grafica de la potencia contra la fase', 1, 0),
(150, 31, 'A', 'Registro de SP', 0, 0),
(151, 31, 'B', 'Registro de temperatura', 1, 0),
(152, 31, 'C', 'Registro de porosidad', 0, 0),
(153, 31, 'D', 'Registro de densidad', 0, 0),
(154, 32, 'A', 'Semblanza', 0, 0),
(155, 32, 'B', 'Factor Q', 0, 0),
(156, 32, 'C', 'Frecuencia', 1, 0),
(157, 32, 'D', 'Impedancia', 0, 0),
(158, 33, 'A', 'Dinamita', 0, 0),
(159, 33, 'B', 'Vibroseis', 0, 0),
(160, 33, 'C', 'Airgun	', 1, 0),
(161, 33, 'D', 'Geoflex', 0, 0),
(162, 34, 'A', 'Ohm-metro', 0, 0),
(163, 34, 'B', 'Gamas-ohm', 0, 0),
(164, 34, 'C', 'Milisiemens-metro	', 1, 0),
(165, 34, 'D', 'Amperes-metro', 0, 0),
(166, 35, 'A', 'Convolución.', 0, 0),
(167, 35, 'B', 'Correlación.', 0, 0),
(168, 35, 'C', 'Deconvolución.', 1, 0),
(169, 35, 'D', 'Autocorrelación', 0, 0),
(170, 36, 'A', 'Método de Slingram', 0, 0),
(171, 36, 'B', 'Dipolo Dipolo', 1, 0),
(172, 37, 'A', 'Potencial espontáneo', 1, 0),
(173, 37, 'B', 'Resistividad', 0, 0),
(174, 37, 'C', 'Rayos gamma', 0, 0),
(175, 37, 'D', 'Densidad', 0, 0),
(176, 38, 'A', 'Apilamiento', 0, 0),
(177, 38, 'B', 'Filtrado multicanal', 0, 0),
(178, 38, 'C', 'Deconvolución', 0, 0),
(179, 38, 'D', 'Migración', 1, 0),
(180, 39, 'A', 'Rotacionales', 0, 0),
(181, 39, 'B', 'No conservativos', 0, 0),
(182, 39, 'C', 'Potenciales', 1, 0),
(183, 39, 'D', 'No potenciales', 0, 0),
(184, 40, 'A', 'FALSO', 0, 0),
(185, 40, 'B', 'VERDADERO', 1, 0),
(186, 41, 'A', 'La atenuación de difracciones', 0, 0),
(187, 41, 'B', 'La atenuación de múltiples', 1, 0),
(188, 41, 'C', 'La atenuación de ondas descendentes', 0, 0),
(189, 41, 'D', 'La atenuación de ondas superficiales', 0, 0),
(190, 42, 'A', 'Por aire libre ', 0, 0),
(191, 42, 'B', 'De Bouguer ', 0, 0),
(192, 42, 'C', 'Por deriva ', 1, 0),
(193, 42, 'D', 'Por mareas', 0, 0),
(194, 43, 'A', 'Un polo magnético ', 0, 0),
(195, 43, 'B', 'Un dipolo eléctrico ', 0, 0),
(196, 43, 'C', 'Un dipolo magnético ', 1, 0),
(197, 43, 'D', 'Un polo eléctrico', 0, 0),
(198, 44, 'A', 'Deconvolución', 0, 0),
(199, 44, 'B', 'Correlación', 0, 0),
(200, 44, 'C', 'Convolución', 1, 0),
(201, 44, 'D', 'Autocorrelación', 0, 0),
(202, 45, 'A', 'Diurna', 1, 0),
(203, 45, 'B', 'Secular', 0, 0),
(204, 45, 'C', 'Polo magnético', 0, 0),
(205, 45, 'D', 'Por elevación', 0, 0),
(206, 46, 'A', 'Rocas metamórficas', 0, 0),
(207, 46, 'B', 'Rocas sedimentarias', 1, 0),
(208, 46, 'C', 'Rocas intrusivas ', 0, 0),
(209, 46, 'D', 'Rocas extrusivas', 0, 0),
(210, 47, 'A', 'Resistencia eléctrica', 0, 0),
(211, 47, 'B', 'Resistividad eléctrica', 1, 0),
(212, 47, 'C', 'Fuerza eléctrica', 0, 0),
(213, 47, 'D', 'Campo eléctrico', 0, 0),
(214, 48, 'A', 'FALSO', 1, 0),
(215, 48, 'B', 'VERDADERO', 0, 0),
(216, 49, 'A', 'Seguridad', 0, 0),
(217, 49, 'B', 'Gestoría', 1, 0),
(218, 49, 'C', 'Topografía', 0, 0),
(219, 49, 'D', 'Perforación', 0, 0),
(220, 50, 'A', '50a.png', 1, 1),
(221, 50, 'B', '50b.png', 0, 1),
(222, 50, 'C', '50c.png', 0, 1),
(223, 50, 'D', '50d.png', 0, 1),
(224, 51, 'A', 'Impedancia eléctrica', 0, 0),
(225, 51, 'B', 'Impedancia electromagnética', 1, 0),
(226, 51, 'C', 'Impedancia magnética', 0, 0),
(227, 51, 'D', 'Impedancia acústica', 0, 0),
(228, 52, 'A', 'Tormentas solares	', 0, 0),
(229, 52, 'B', 'Líneas de alta tensión', 0, 0),
(230, 52, 'C', 'Actividad meteorológica', 1, 0),
(231, 52, 'D', 'Actividad humana', 0, 0),
(232, 53, 'A', 'Densidad', 0, 0),
(233, 53, 'B', 'Sónico', 1, 0),
(234, 53, 'C', 'Resistividad', 0, 0),
(235, 53, 'D', 'Rayos gamma', 0, 0),
(236, 54, 'A', 'FALSO', 0, 0),
(237, 54, 'B', 'VERDADERO', 1, 0),
(238, 55, 'A', 'FALSO', 0, 0),
(239, 55, 'B', 'VERDADERO', 1, 0),
(240, 56, 'A', 'Se mantiene igual	', 0, 0),
(241, 56, 'B', 'Disminuye lentamente	', 0, 0),
(242, 56, 'C', 'Disminuye bruscamente	', 0, 0),
(243, 56, 'D', 'Aumenta', 1, 0),
(244, 57, 'A', 'Separación entre la fuente y el receptor', 1, 0),
(245, 57, 'B', 'Distancia entre fuentes', 0, 0),
(246, 57, 'C', 'Separación entre receptores', 0, 0),
(247, 57, 'D', 'Distancia entre la fuente y el último receptor', 0, 0),
(248, 58, 'A', 'FALSO', 1, 0),
(249, 58, 'B', 'VERDADERO', 0, 0),
(250, 59, 'A', 'FALSO', 1, 0),
(251, 59, 'B', 'VERDADERO', 0, 0),
(252, 60, 'A', 'FALSO', 1, 0),
(253, 60, 'B', 'VERDADERO', 0, 0),
(254, 61, 'A', 'Isométrico', 1, 0),
(255, 61, 'B', 'Tetragonal', 0, 0),
(256, 61, 'C', 'Triclínico', 0, 0),
(257, 61, 'D', 'Hexagonal', 0, 0),
(258, 62, 'A', 'Piroxenos', 1, 0),
(259, 62, 'B', 'Anfíboles', 0, 0),
(260, 62, 'C', 'Micas.', 0, 0),
(261, 62, 'D', 'Feldespatos.', 0, 0),
(262, 63, 'A', 'Cobre', 0, 0),
(263, 63, 'B', 'Zinc', 0, 0),
(264, 63, 'C', 'Plomo', 0, 0),
(265, 63, 'D', 'Aluminio', 1, 0),
(266, 64, 'A', 'Sistema dentrítico', 1, 0),
(267, 64, 'B', 'Sistema meandrítico', 0, 0),
(268, 64, 'C', 'Sistema pluvial', 0, 0),
(269, 64, 'D', 'Sistema lótico', 0, 0),
(270, 65, 'A', 'Ley de sucesión faunística', 1, 0),
(271, 65, 'B', 'Ley de Darwin', 0, 0),
(272, 65, 'C', 'Ley de duración de las especies', 0, 0),
(273, 65, 'D', 'Ley de distribución de las especies', 0, 0),
(274, 66, 'A', 'Metamorfismo hidrotermal', 0, 0),
(275, 66, 'B', 'Metamorfismo regional', 0, 0),
(276, 66, 'C', 'Metamorfismo de contacto', 1, 0),
(277, 66, 'D', 'Metamorfismo local', 0, 0),
(278, 67, 'A', 'Gasterópodos', 0, 0),
(279, 67, 'B', 'Monoplacoforos', 0, 0),
(280, 67, 'C', 'Moluscos', 1, 0),
(281, 67, 'D', 'Coleideos', 0, 0),
(282, 68, 'A', 'Comportamiento térmico', 0, 0),
(283, 68, 'B', 'Extensión y fallamiento', 0, 0),
(284, 68, 'C', 'Eustatismo', 1, 0),
(285, 68, 'D', 'Flexión de la litosfera', 0, 0),
(286, 69, 'A', 'Exfoliación y fractura', 1, 0),
(287, 69, 'B', 'Rayadura y clivaje', 0, 0),
(288, 70, 'A', 'Orogenia Caledania', 0, 0),
(289, 70, 'B', 'Orogenia Cascadiana', 0, 0),
(290, 70, 'C', 'Orogenia Laramide', 1, 0),
(291, 70, 'D', 'Orogenia Alpina', 0, 0),
(292, 71, 'A', 'Límite K-T', 1, 0),
(293, 71, 'B', 'Límite P-T', 0, 0),
(294, 71, 'C', 'Límite J-K', 0, 0),
(295, 71, 'D', 'Límite T-C', 0, 0),
(296, 72, 'A', 'Hidrógeno', 0, 0),
(297, 72, 'B', 'Nitrógeno', 0, 0),
(298, 72, 'C', 'Oxigeno', 1, 0),
(299, 72, 'D', 'Carbono', 0, 0),
(300, 73, 'A', '55 a 66 +/- 5 ma', 0, 0),
(301, 73, 'B', '213 a 248 +/- 5 ma', 0, 0),
(302, 73, 'C', '145 a 213 +/- 5 ma', 0, 0),
(303, 73, 'D', '66 a 145 +/- 5 ma', 1, 0),
(304, 74, 'A', 'Composición mineralógica y texturas.', 1, 0),
(305, 74, 'B', 'Cambios de temperatura y presión.', 0, 0),
(306, 75, 'A', 'Biotita', 0, 0),
(307, 75, 'B', 'Calcita', 0, 0),
(308, 75, 'C', 'Galena', 0, 0),
(309, 75, 'D', 'Magnetita', 1, 0),
(310, 76, 'A', 'Datación radiométrica', 0, 0),
(311, 76, 'B', 'Datación geomagnética', 0, 0),
(312, 76, 'C', 'Datación paleoclimática', 1, 0),
(313, 76, 'D', 'Datación geomorfológica', 0, 0),
(314, 77, 'A', '1', 0, 0),
(315, 77, 'B', '2', 0, 0),
(316, 77, 'C', '3', 1, 0),
(317, 77, 'D', '4', 0, 0),
(318, 78, 'A', 'Geoquímica orgánica', 1, 0),
(319, 78, 'B', 'Palinología', 0, 0),
(320, 78, 'C', 'Geoquímica inorgánica', 0, 0),
(321, 78, 'D', 'Paleontología', 0, 0),
(322, 79, 'A', 'Lagunar', 1, 0),
(323, 79, 'B', 'Marino somero', 0, 0),
(324, 79, 'C', 'Deltaico', 0, 0),
(325, 79, 'D', 'Fluvial', 0, 0),
(326, 80, 'A', 'Inversa', 0, 0),
(327, 80, 'B', 'Normal', 0, 0),
(328, 80, 'C', 'Transformante', 1, 0),
(329, 80, 'D', 'Cabalgadura', 0, 0),
(330, 81, 'A', 'Quelicerados', 0, 0),
(331, 81, 'B', 'Trilobites', 1, 0),
(332, 81, 'C', 'Mandibulados', 0, 0),
(333, 81, 'D', 'Amonitas', 0, 0),
(334, 82, 'A', 'Biología', 0, 0),
(335, 82, 'B', 'Bioestratigrafía', 0, 0),
(336, 82, 'C', 'Paleontología', 0, 0),
(337, 82, 'D', 'Palinología', 1, 0),
(338, 83, 'A', 'Lateral', 0, 0),
(339, 83, 'B', 'Tijera', 0, 0),
(340, 83, 'C', 'Lístrica', 1, 0),
(341, 83, 'D', 'Normal', 0, 0),
(342, 84, 'A', 'Local', 0, 0),
(343, 84, 'B', 'Compresional', 1, 0),
(344, 84, 'C', 'Extensional', 0, 0),
(345, 84, 'D', 'Transcurrentes', 0, 0),
(346, 85, 'A', 'Asimétrico', 0, 0),
(347, 85, 'B', 'Rollover', 1, 0),
(348, 85, 'C', 'Kink', 0, 0),
(349, 85, 'D', 'Caja', 0, 0),
(350, 86, 'A', 'Llanuras de inundación ', 0, 0),
(351, 86, 'B', 'Abanicos Aluviales ', 1, 0),
(352, 86, 'C', 'Ambientes neríticos', 0, 0),
(353, 86, 'D', 'Dunas', 0, 0),
(354, 87, 'A', 'Aragonita', 1, 0),
(355, 87, 'B', 'Glauconita', 0, 0),
(356, 87, 'C', 'Pirita', 0, 0),
(357, 87, 'D', 'Anhidrita', 0, 0),
(358, 88, 'A', 'Conocer el origen de las rocas', 0, 0),
(359, 88, 'B', 'Determinar los rasgos estructurales', 0, 0),
(360, 88, 'C', 'Establecer análogos con el subsuelo', 1, 0),
(361, 88, 'D', 'Conocer los formaciones aflorantes', 0, 0),
(362, 89, 'A', 'Era', 0, 0),
(363, 89, 'B', 'Piso', 1, 0),
(364, 89, 'C', 'Serie', 0, 0),
(365, 89, 'D', 'Sistema', 0, 0),
(366, 90, 'A', 'Intemperismo', 0, 0),
(367, 90, 'B', 'Erosión', 1, 0),
(368, 90, 'C', 'Transporte', 0, 0),
(369, 90, 'D', 'Depósito', 0, 0),
(370, 91, 'A', 'Ley de Poisson', 0, 0),
(371, 91, 'B', 'Ley de Darcy', 1, 0),
(372, 91, 'C', 'Ley de Boyle', 0, 0),
(373, 91, 'D', 'Ley de Neumann', 0, 0),
(374, 92, 'A', 'Limite divergente', 0, 0),
(375, 92, 'B', 'Subducción', 0, 0),
(376, 92, 'C', 'Margen pasivo	', 1, 0),
(377, 92, 'D', 'Margen activo', 0, 0),
(378, 93, 'A', 'Materiales incompetentes', 0, 0),
(379, 93, 'B', 'Materiales sintéticos', 0, 0),
(380, 93, 'C', 'Materiales porosos', 0, 0),
(381, 93, 'D', 'Materiales competentes', 1, 0),
(382, 94, 'A', 'La edad geológica', 0, 0),
(383, 94, 'B', 'El tipo de sedimentos asociados', 0, 0),
(384, 94, 'C', 'La profundidad de la columna de agua ', 1, 0),
(385, 94, 'D', 'El tipo de ambiente sedimentario', 0, 0),
(386, 95, 'A', 'Rodinia', 1, 0),
(387, 95, 'B', 'Gondwana', 0, 0),
(388, 95, 'C', 'Laurasia', 0, 0),
(389, 95, 'D', 'Pangea', 0, 0),
(390, 96, 'A', 'Mesozoico', 1, 0),
(391, 96, 'B', 'Paleozoico', 0, 0),
(392, 96, 'C', 'Cenozoico', 0, 0),
(393, 96, 'D', 'Proterozoica', 0, 0),
(394, 97, 'A', 'África', 0, 0),
(395, 97, 'B', 'Asia', 0, 0),
(396, 97, 'C', 'Norteamérica', 1, 0),
(397, 97, 'D', 'Sudamérica', 0, 0),
(398, 98, 'A', 'Ordovícico	', 0, 0),
(399, 98, 'B', 'Cretácico', 0, 0),
(400, 98, 'C', 'Pérmico', 1, 0),
(401, 98, 'D', 'Triásico', 0, 0),
(402, 99, 'A', 'FALSO', 0, 0),
(403, 99, 'B', 'VERDADERO', 1, 0),
(404, 100, 'A', 'Jean-Baptiste de Lamarck', 0, 0),
(405, 100, 'B', 'Charles Robert Darwin', 0, 0),
(406, 100, 'C', 'William Smith', 1, 0),
(407, 100, 'D', 'Alexandre Brogniart', 0, 0),
(408, 101, 'A', 'Diagénesis', 0, 0),
(409, 101, 'B', 'Sedimentación', 0, 0),
(410, 101, 'C', 'Intemperismo', 0, 0),
(411, 101, 'D', 'Litificación', 1, 0),
(412, 102, 'A', 'Cuenca', 1, 0),
(413, 102, 'B', 'Sinclinal', 0, 0),
(414, 102, 'C', 'Cuenca intracratónica	', 0, 0),
(415, 102, 'D', 'Geosinclinal', 0, 0),
(416, 103, 'A', 'Lístrica', 0, 0),
(417, 103, 'B', 'Falla de crecimiento', 1, 0),
(418, 103, 'C', 'Tijera', 0, 0),
(419, 103, 'D', 'Normal', 0, 0),
(420, 104, 'A', 'FALSO	', 1, 0),
(421, 104, 'B', 'VERDADERO', 0, 0),
(422, 105, 'A', 'Inversa', 1, 0),
(423, 105, 'B', 'Normal', 0, 0),
(424, 105, 'C', 'Transcurrentes', 0, 0),
(425, 105, 'D', 'Lístricas', 0, 0),
(426, 106, 'A', 'FALSO', 1, 0),
(427, 106, 'B', 'VERDADERO', 0, 0),
(428, 107, 'A', 'FALSO', 1, 0),
(429, 107, 'B', 'VERDADERO', 0, 0),
(430, 108, 'A', 'Mesosfera', 0, 0),
(431, 108, 'B', 'Litosfera', 1, 0),
(432, 108, 'C', 'Troposfera', 0, 0),
(433, 108, 'D', 'Criosfera', 0, 0),
(434, 109, 'A', 'FALSO', 0, 0),
(435, 109, 'B', 'VERDADERO', 1, 0),
(436, 110, 'A', 'Sapropelico', 0, 0),
(437, 110, 'B', 'Húmico', 0, 0),
(438, 110, 'C', 'Sapropelico o Húmico', 1, 0),
(439, 110, 'D', 'Ninguna de las anteriores', 0, 0),
(440, 111, 'A', 'FALSO', 1, 0),
(441, 111, 'B', 'VERDADERO', 0, 0),
(442, 112, 'A', 'Porosidad', 0, 0),
(443, 112, 'B', 'Permeabilidad', 0, 0),
(444, 113, 'A', 'Catagenesis', 0, 0),
(445, 113, 'B', 'Diagénesis', 1, 0),
(446, 113, 'C', 'Metagénesis', 0, 0),
(447, 113, 'D', 'Sedimentación', 0, 0),
(448, 114, 'A', 'Lodolita', 0, 0),
(449, 114, 'B', 'Limolitas	', 1, 0),
(450, 114, 'C', 'Guijarro', 0, 0),
(451, 114, 'D', 'Litarenita', 0, 0),
(452, 115, 'A', 'Wacka de cuarzo', 0, 0),
(453, 115, 'B', 'Wacka lítica', 1, 0),
(454, 115, 'C', 'Wacka feldespatica', 0, 0),
(455, 115, 'D', 'Grauvaca', 0, 0),
(456, 116, 'A', 'Hierro', 0, 0),
(457, 116, 'B', 'Oxigeno', 1, 0),
(458, 116, 'C', 'Magnesio', 0, 0),
(459, 116, 'D', 'Calcio', 0, 0),
(460, 117, 'A', 'Mineralización', 0, 0),
(461, 117, 'B', 'Exfoliación', 0, 0),
(462, 117, 'C', 'Cristalogénesis', 1, 0),
(463, 117, 'D', 'Concreción', 0, 0),
(464, 118, 'A', 'FALSO', 0, 0),
(465, 118, 'B', 'VERDADERO', 1, 0),
(466, 119, 'A', 'FALSO', 0, 0),
(467, 119, 'B', 'VERDADERO', 1, 0),
(468, 120, 'A', 'FALSO', 1, 0),
(469, 120, 'B', 'VERDADERO', 0, 0),
(684, 121, 'A', 'Tubería de Perforación', 0, 0),
(685, 121, 'B', 'Tubería de Revestimiento', 1, 0),
(686, 121, 'C', 'Tubería de Producción', 0, 0),
(687, 121, 'D', 'Tubería de Ademe', 0, 0),
(688, 122, 'A', 'FALSO', 0, 0),
(689, 122, 'B', 'VERDADERO', 1, 0),
(690, 123, 'A', 'Arietes ciegos', 0, 0),
(691, 123, 'B', 'Arietes de tubería', 1, 0),
(692, 123, 'C', 'Arietes variables', 0, 0),
(693, 123, 'D', 'Ninguno', 0, 0),
(694, 124, 'A', 'Resistencia o fuerza de gel', 0, 0),
(695, 124, 'B', 'Dureza y MBT', 1, 0),
(696, 124, 'C', 'Fluidos Base aceite y airados', 0, 0),
(697, 124, 'D', 'Ninguna', 0, 0),
(698, 125, 'A', 'Aceite Ligero', 0, 0),
(699, 125, 'B', 'Aceite Pesado', 0, 0),
(700, 125, 'C', 'Aceite superligero', 1, 0),
(701, 125, 'D', 'Aceite extrapesado', 0, 0),
(702, 126, 'A', 'Gas asociado', 1, 0),
(703, 126, 'B', 'Gas húmedo', 0, 0),
(704, 126, 'C', 'Gas natural', 0, 0),
(705, 126, 'D', 'Gas seco', 0, 0),
(706, 127, 'A', 'Identificación de rocas', 0, 0),
(707, 127, 'B', 'Profundidad del pozo', 0, 0),
(708, 127, 'C', 'Detección de presiones anormales', 1, 0),
(709, 127, 'D', 'Clasificación de fósiles', 0, 0),
(710, 128, 'A', 'FALSO', 1, 0),
(711, 128, 'B', 'VERDADERO', 0, 0),
(712, 129, 'A', 'FALSO', 0, 0),
(713, 129, 'B', 'VERDADERO', 1, 0),
(714, 130, 'A', 'Hemisférico', 1, 0),
(715, 130, 'B', 'Esférico', 0, 0),
(716, 130, 'C', 'Radial', 0, 0),
(717, 130, 'D', 'Lineal', 0, 0),
(718, 131, 'A', 'Válvulas', 0, 0),
(719, 131, 'B', 'Preventores', 1, 0),
(720, 131, 'C', 'Kelly', 0, 0),
(721, 131, 'D', 'Sarta', 0, 0),
(722, 132, 'A', 'Pozos de Producción', 0, 0),
(723, 132, 'B', 'Pozos de Desarrollo', 0, 0),
(724, 132, 'C', 'Pozos Marinos', 0, 0),
(725, 132, 'D', 'Pozos Fluyentes', 1, 0),
(726, 133, 'A', 'Proteger la barrena asignada', 0, 0),
(727, 133, 'B', 'Proteger las zonas perforadas', 1, 0),
(728, 133, 'C', 'Proteger los fluidos de perforación', 0, 0),
(729, 133, 'D', 'Proteger la sarta de perforación', 0, 0),
(730, 134, 'A', 'Gas y condensado', 1, 0),
(731, 134, 'B', 'Aceite y gas disuelto ', 0, 0),
(732, 134, 'C', 'Aceite', 0, 0),
(733, 134, 'D', 'Gas', 0, 0),
(734, 135, 'A', 'FALSO', 1, 0),
(735, 135, 'B', 'VERDADERO', 0, 0),
(736, 136, 'A', 'Protección mecánica', 0, 0),
(737, 136, 'B', 'Esmaltado', 0, 0),
(738, 136, 'C', 'Fibra de vidrio', 0, 0),
(739, 136, 'D', 'Protección catódica', 1, 0),
(740, 137, 'A', 'Detección de gas', 0, 0),
(741, 137, 'B', 'Análisis de arenamiento', 1, 0),
(742, 137, 'C', 'Detección de aceite', 0, 0),
(743, 137, 'D', 'Sismograma sintético', 0, 0),
(744, 138, 'A', 'Gas Natural', 0, 0),
(745, 138, 'B', 'Gas Disuelto', 1, 0),
(746, 138, 'C', 'Gas Asociado', 0, 0),
(747, 138, 'D', 'Condensados del Gas', 0, 0),
(748, 139, 'A', 'FALSO', 1, 0),
(749, 139, 'B', 'VERDADERO', 0, 0),
(750, 140, 'A', '90, 000 psi', 0, 0),
(751, 140, 'B', '110, 000 kg/cm2', 0, 0),
(752, 140, 'C', '110,000 psi', 1, 0),
(753, 140, 'D', '110, 000 bars', 0, 0),
(754, 141, 'A', 'Ecuación de Barlow', 1, 0),
(755, 141, 'B', 'Ecuación de Eaton', 0, 0),
(756, 141, 'C', 'Ecuación de Young', 0, 0),
(757, 141, 'D', 'Ecuación de Poisson', 0, 0),
(758, 142, 'A', 'FALSO', 0, 0),
(759, 142, 'B', 'VERDADERO', 1, 0),
(760, 143, 'A', 'FALSO', 0, 0),
(761, 143, 'B', 'VERDADERO', 1, 0),
(762, 144, 'A', 'Flujo burbuja, ondulado, bache y laminado. ', 0, 0),
(763, 144, 'B', 'Flujo bache, anular, laminado y niebla.', 0, 0),
(764, 144, 'C', 'Flujo niebla, anular y  ondulad.', 0, 0),
(765, 144, 'D', 'Flujo burbuja, bache, anular y niebla. ', 1, 0),
(766, 145, 'A', 'De Arnold', 1, 0),
(767, 145, 'B', 'De Stock', 0, 0),
(768, 145, 'C', 'De Sauders-Brown', 0, 0),
(769, 145, 'D', 'De Watkins', 0, 0),
(770, 146, 'A', 'Fuerza Centrífuga', 0, 0),
(771, 146, 'B', 'Presión diferencial', 1, 0),
(772, 146, 'C', 'Desplazamiento Positivo', 0, 0),
(773, 146, 'D', 'Fuerza Coriolis', 0, 0),
(774, 147, 'A', 'Colas Tanque', 0, 0),
(775, 147, 'B', 'Recuperación Secundaria', 0, 0),
(776, 147, 'C', 'Sistemas artificiales', 1, 0),
(777, 147, 'D', 'Terminaciones Inteligentes', 0, 0),
(778, 148, 'A', 'Capacidad de Flujo', 0, 0),
(779, 148, 'B', 'Saturación Crítica', 0, 0),
(780, 148, 'C', 'Penetración Parcial', 0, 0),
(781, 148, 'D', 'Índice de productividad', 1, 0),
(782, 149, 'A', 'Factor volumétrico del gas', 1, 0),
(783, 149, 'B', 'Factor de compresibilidad del gas', 0, 0),
(784, 149, 'C', 'Coeficiente de compresibilidad isotérmico del gas', 0, 0),
(785, 149, 'D', 'Ninguna de los tres', 0, 0),
(786, 150, 'A', 'Impulsor', 0, 0),
(787, 150, 'B', 'Rotor', 0, 0),
(788, 150, 'C', 'Varilla', 0, 0),
(789, 150, 'D', 'Estator', 1, 0),
(790, 151, 'A', 'Gas Disuelto', 0, 0),
(791, 151, 'B', 'Gas Asociado', 0, 0),
(792, 151, 'C', 'Gas No Asociado', 1, 0),
(793, 151, 'D', 'Condensados del Gas', 0, 0),
(794, 152, 'A', 'Benceno, Nafteno y Tolueno', 0, 0),
(795, 152, 'B', 'Benceno, Tolueno, Xileno', 1, 0),
(796, 152, 'C', 'Fenantreno, Xileno y Nafteno.', 0, 0),
(797, 152, 'D', 'Tolueno, Ortoxileno y Benceno. ', 0, 0),
(798, 153, 'A', 'Mc Cain & Mohammad', 0, 0),
(799, 153, 'B', 'Duns, Ros & Orkizewsky.', 0, 0),
(800, 153, 'C', 'Matthews-Brons-Hazebroek', 1, 0),
(801, 153, 'D', 'Voguel, Standing & Fetkovich.', 0, 0),
(802, 154, 'A', 'Ley de Darcy', 0, 0),
(803, 154, 'B', 'Efecto de Gas natural', 0, 0),
(804, 154, 'C', 'Ley de Stokes', 0, 0),
(805, 154, 'D', 'Efecto Klinkenberg', 1, 0),
(806, 155, 'A', 'Permeabilidad/Viscosidad', 0, 0),
(807, 155, 'B', 'Permeabilidad*Espesor', 1, 0),
(808, 155, 'C', 'Permeabilidad/Espesor', 0, 0),
(809, 155, 'D', 'Permeabilidad*Viscosidad', 0, 0),
(810, 156, 'A', 'Método MDH - Línea Recta', 0, 0),
(811, 156, 'B', 'Gráfico de Horner - Línea Recta', 0, 0),
(812, 156, 'C', 'Curva Tipo', 1, 0),
(813, 156, 'D', 'Función Derivada de Bourdet', 0, 0),
(814, 157, 'A', 'Ecuación de flujo fraccional', 1, 0),
(815, 157, 'B', 'Ecuación de Avance Frontal', 0, 0),
(816, 157, 'C', 'Ecuación de Flujo Parcial', 0, 0),
(817, 157, 'D', 'Ecuación de Avance Parcial', 0, 0),
(818, 158, 'A', '1', 0, 0),
(819, 158, 'B', '0', 1, 0),
(820, 158, 'C', '10', 0, 0),
(821, 158, 'D', '45°', 0, 0),
(822, 159, 'A', 'Agarwal', 0, 0),
(823, 159, 'B', 'Fetkovich', 0, 0),
(824, 159, 'C', 'Carter-Tracy', 0, 0),
(825, 159, 'D', 'Van Everdingen & Hurst', 1, 0),
(826, 160, 'A', 'Método del mínimo esfuerzo', 0, 0),
(827, 160, 'B', 'Método de dirección del pozo', 0, 0),
(828, 160, 'C', 'Método de la pata de perro', 0, 0),
(829, 160, 'D', 'Método de mínima curvatura', 1, 0),
(830, 161, 'A', 'Yacimientos de Gas Seco', 1, 0),
(831, 161, 'B', 'Yacimientos de Gas Húmedo', 0, 0),
(832, 162, 'A', 'Yacimiento saturado', 0, 0),
(833, 162, 'B', 'Yacimiento de empuje volumétrico', 0, 0),
(834, 162, 'C', 'Yacimiento bajo saturado', 1, 0),
(835, 162, 'D', 'Yacimiento naturalmente fracturado', 0, 0),
(836, 163, 'A', 'Factor Volumétrico del Aceite', 0, 0),
(837, 163, 'B', 'Relación Gas Aceite Producido', 0, 0),
(838, 163, 'C', 'Factor de Recuperación de Aceite', 1, 0),
(839, 163, 'D', 'Pronostico de Producción', 0, 0),
(840, 164, 'A', 'Análisis Cromatográfico', 0, 0),
(841, 164, 'B', 'Análisis PVT', 0, 0),
(842, 164, 'C', 'Análisis de Fluidos', 0, 0),
(843, 164, 'D', 'Diagramas de Fase', 1, 0),
(844, 165, 'A', 'Saturación Irreductible', 0, 0),
(845, 165, 'B', 'Saturación Crítica', 1, 0),
(846, 165, 'C', 'Saturación Bifásica', 0, 0),
(847, 165, 'D', 'Saturación Mojante', 0, 0),
(848, 166, 'A', 'Saturación Critica', 0, 0),
(849, 166, 'B', 'Saturación Móvil', 0, 0),
(850, 166, 'C', 'Saturación Residual', 1, 0),
(851, 166, 'D', 'Saturación Inicial', 0, 0),
(852, 167, 'A', 'Factor volumétrico del aceite', 0, 0),
(853, 167, 'B', 'Factor de presión volumétrico', 0, 0),
(854, 167, 'C', 'Encogimiento dl fluido', 0, 0),
(855, 167, 'D', 'Compresibilidad de un fluido', 1, 0),
(856, 168, 'A', 'Monte Carlo', 1, 0),
(857, 168, 'B', 'Volumétrico', 0, 0),
(858, 168, 'C', 'Simulación numérica de Yacimientos', 0, 0),
(859, 168, 'D', 'Balance de materia', 0, 0),
(860, 169, 'A', 'FALSO', 1, 0),
(861, 169, 'B', 'VERDADERO', 0, 0),
(862, 170, 'A', 'Invasión de lodo', 0, 0),
(863, 170, 'B', 'Zonas ladronas', 0, 0),
(864, 170, 'C', 'Pérdidas de circulación ', 1, 0),
(865, 170, 'D', 'Ruptura del cono de agua', 0, 0),
(866, 171, 'A', 'Reparación menor', 0, 0),
(867, 171, 'B', 'Reparación mayor', 0, 0),
(868, 171, 'C', 'Colocación de tapón', 0, 0),
(869, 171, 'D', 'Cementación forzada', 1, 0),
(870, 172, 'A', 'Separadores', 0, 0),
(871, 172, 'B', 'Tanques', 1, 0),
(872, 172, 'C', 'Ductos', 0, 0),
(873, 172, 'D', 'Pozos', 0, 0),
(874, 173, 'A', 'Cementación Forzada', 0, 0),
(875, 173, 'B', 'Terminación de Pozos ', 1, 0),
(876, 173, 'C', 'Limpieza del Pozo', 0, 0),
(877, 173, 'D', 'Corrida de Registros', 0, 0),
(878, 174, 'A', 'Curva de Burbujeo', 0, 0),
(879, 174, 'B', 'Punto Cricondebarico', 0, 0),
(880, 174, 'C', 'Curva de Rocío', 1, 0),
(881, 174, 'D', 'Punto Cricondentérmico', 0, 0),
(882, 175, 'A', 'Punto Triple', 0, 0),
(883, 175, 'B', 'Estado Isentrópico', 0, 0),
(884, 175, 'C', 'Estado Neutro', 0, 0),
(885, 175, 'D', 'Punto Crítico', 1, 0),
(886, 176, 'A', 'Por Segregación Gravitacional', 1, 0),
(887, 176, 'B', 'Por Columna Hidroestática', 0, 0),
(888, 176, 'C', 'Por Presión Osmótica', 0, 0),
(889, 176, 'D', 'Por Empuje por Densidades', 0, 0),
(890, 177, 'A', 'FALSO', 0, 0),
(891, 177, 'B', 'VERDADERO', 1, 0),
(892, 178, 'A', 'FALSO', 0, 0),
(893, 178, 'B', 'VERDADERO', 1, 0),
(894, 179, 'A', 'FALSO', 0, 0),
(895, 179, 'B', 'VERDADERO', 1, 0),
(896, 180, 'A', 'FALSO', 1, 0),
(897, 180, 'B', 'VERDADERO', 0, 0),
(898, 181, 'A', 'Sísmica 3D', 0, 0),
(899, 181, 'B', 'Sísmica 3C', 0, 0),
(900, 181, 'C', 'Sísmica 4D', 1, 0),
(901, 181, 'D', 'Sísmica 9C', 0, 0),
(902, 182, 'A', 'Gravimetría', 0, 0),
(903, 182, 'B', 'Geología superficial', 0, 0),
(904, 182, 'C', 'Magnetometría', 1, 0),
(905, 182, 'D', 'Geología de subsuelo', 0, 0),
(906, 183, 'A', 'FALSO', 0, 0),
(907, 183, 'B', 'VERDADERO', 1, 0),
(910, 184, 'A', 'Isometría.', 0, 0),
(911, 184, 'B', 'Isolinea.', 0, 0),
(912, 184, 'C', 'Isostasia.', 1, 0),
(913, 184, 'D', 'geoide', 0, 0),
(914, 185, 'A', 'Registros de SP y Rayos Gamma', 0, 0),
(915, 185, 'B', 'Registros de densidad y de neutrones', 0, 0),
(916, 185, 'C', 'Registros de litodensidad y SP', 0, 0),
(917, 185, 'D', 'Registros de caliper y de imagen sónica dipolar', 1, 0),
(918, 186, 'A', 'La corteza y el manto', 0, 0),
(919, 186, 'B', 'El manto y el núcleo', 1, 0),
(920, 186, 'C', 'Dentro de la corteza', 0, 0),
(921, 186, 'D', 'Dentro del núcleo', 0, 0),
(922, 187, 'A', 'Lípidos y lignitos', 1, 0),
(923, 187, 'B', 'Benceno, sulfuros ', 0, 0),
(924, 187, 'C', 'Kerógeno y bitumen', 0, 0),
(925, 187, 'D', 'Grasas y azucares ', 0, 0),
(926, 188, 'A', 'Bitumen', 0, 0),
(927, 188, 'B', 'Carbón', 0, 0),
(928, 188, 'C', 'Benceno', 0, 0),
(929, 188, 'D', 'Kerógeno ', 1, 0),
(930, 189, 'A', 'Dow y O’Conor ', 0, 0),
(931, 189, 'B', 'Tissot y Welte ', 1, 0),
(932, 189, 'C', 'Van Krevelen ', 0, 0),
(933, 189, 'D', 'Taoist', 0, 0),
(934, 190, 'A', 'SLR', 0, 0),
(935, 190, 'B', 'LWD', 1, 0),
(936, 190, 'C', 'IVA', 0, 0),
(937, 190, 'D', 'ISR', 0, 0),
(938, 191, 'A', 'Actividad magnética de la tierra', 0, 0),
(939, 191, 'B', 'Actividad electroquímica del subsuelo', 1, 0),
(940, 191, 'C', 'Actividad radioeléctrica del subsuelo', 0, 0),
(941, 191, 'D', 'Actividad sísmica del subsuelo', 0, 0),
(942, 192, 'A', 'FALSO', 0, 0),
(943, 192, 'B', 'VERDADERO', 1, 0),
(946, 193, 'A', 'Sabinas', 0, 0),
(947, 193, 'B', 'Burgos', 0, 0),
(948, 193, 'C', 'Eagle Ford', 0, 0),
(949, 193, 'D', 'Ninguna de las anteriores', 1, 0),
(950, 194, 'A', 'FALSO', 0, 0),
(951, 194, 'B', 'VERDADERO', 1, 0),
(954, 195, 'A', 'Gravimétrico', 0, 0),
(955, 195, 'B', 'Magnetométrico', 0, 0),
(956, 195, 'C', 'Sismológico', 1, 0),
(957, 195, 'D', 'Electromagnético', 0, 0),
(958, 196, 'A', 'Zona granítica', 0, 0),
(959, 196, 'B', 'Zona basáltica', 1, 0),
(960, 196, 'C', 'Zona abisal', 0, 0),
(961, 196, 'D', 'Zona sedimentaria', 0, 0),
(962, 197, 'A', 'Las ondas sísmicas producidas por los terremotos', 1, 0),
(963, 197, 'B', ' El análisis de inclusiones provenientes del núcleo', 0, 0),
(964, 197, 'C', 'Los sedimentos de aguas profundas', 0, 0),
(965, 197, 'D', 'Los basaltos magnetizados', 0, 0),
(966, 198, 'A', 'Cretácico Inferior', 0, 0),
(967, 198, 'B', 'Jurásico Superior', 0, 0),
(968, 198, 'C', 'Cretácico Medio', 0, 0),
(969, 198, 'D', 'Cretácico Superior', 1, 0),
(970, 199, 'A', 'FALSO', 1, 0),
(971, 199, 'B', 'VERDADERO', 0, 0),
(974, 200, 'A', 'Una localización', 0, 0),
(975, 200, 'B', 'Un objetivo exploratorio', 1, 0),
(976, 200, 'C', 'Una estrategia', 0, 0),
(977, 200, 'D', 'Una perforación de un pozo', 0, 0),
(978, 201, 'A', 'Estratigráficas', 0, 0),
(979, 201, 'B', 'Sedimentológicas', 0, 0),
(980, 201, 'C', 'Geométricas', 1, 0),
(981, 201, 'D', 'Geoquímicas', 0, 0),
(982, 202, 'A', 'Kerógeno Tipo II', 0, 0),
(983, 202, 'B', 'Kerógeno Tipo I', 1, 0),
(984, 202, 'C', 'Kerógeno Tipo IV', 0, 0),
(985, 202, 'D', 'Kerógeno Tipo III', 0, 0),
(986, 203, 'A', 'Densidad', 0, 0),
(987, 203, 'B', 'Sónico', 1, 0),
(988, 203, 'C', 'Resistivo', 0, 0),
(989, 203, 'D', 'Rayos Gama', 0, 0),
(990, 204, 'A', 'La zona de sombra de las ondas S y las ondas P', 0, 0),
(991, 204, 'B', 'Corteza y Manto', 1, 0),
(992, 204, 'C', 'Litósfera y Astenósfera', 0, 0),
(993, 204, 'D', 'Manto y Nucleo', 0, 0),
(994, 205, 'A', 'Saligradiente', 0, 0),
(995, 205, 'B', 'Termoclina', 0, 0),
(996, 205, 'C', 'Saliclina', 0, 0),
(997, 205, 'D', 'Haloclina', 1, 0),
(998, 206, 'A', 'Determinar una Columna geologica ', 0, 0),
(999, 206, 'B', 'Obtener velocidades de pozos', 0, 0),
(1000, 206, 'C', 'Posicionar la columna geológica del pozo en la sísmica', 1, 0),
(1001, 206, 'D', 'Graficar velocidades de pozo', 0, 0),
(1002, 207, 'A', 'FALSO', 1, 0),
(1003, 207, 'B', 'VERDADERO', 0, 0),
(1006, 208, 'A', 'Paleocañón de Chicontepec', 0, 0),
(1007, 208, 'B', 'Tampico Misantla', 1, 0),
(1008, 208, 'C', 'Burgos', 0, 0),
(1009, 208, 'D', 'Veracruz', 0, 0),
(1010, 209, 'A', 'Nitrogeno y Carbono', 0, 0),
(1011, 209, 'B', 'Hidrogeno y Carbono', 1, 0),
(1012, 209, 'C', 'Fosforo y Carbono', 0, 0),
(1013, 209, 'D', 'Helio y Carbono', 0, 0),
(1014, 210, 'A', 'Corteza', 0, 0),
(1015, 210, 'B', 'Manto', 0, 0),
(1016, 210, 'C', 'Núcleo Externo', 1, 0),
(1017, 210, 'D', 'Núcleo Interno', 0, 0),
(1018, 211, 'A', 'Lehmann', 0, 0),
(1019, 211, 'B', 'Gutenberg', 0, 0),
(1020, 211, 'C', 'Mohorovicic', 1, 0),
(1021, 211, 'D', 'Conrad', 0, 0),
(1022, 212, 'A', 'Compactación', 0, 0),
(1023, 212, 'B', 'Presión', 0, 0),
(1024, 212, 'C', 'Temperatura', 1, 0),
(1025, 212, 'D', 'Tiempo', 0, 0),
(1026, 213, 'A', 'Basalto', 0, 0),
(1027, 213, 'B', 'Granito', 0, 0),
(1028, 213, 'C', 'Riolita', 1, 0),
(1029, 213, 'D', 'Gabro', 0, 0),
(1030, 214, 'A', 'El modelo geológico', 1, 0),
(1031, 214, 'B', 'El modelo geofísico', 0, 0),
(1032, 214, 'C', 'El modelo sedimentario ', 0, 0),
(1033, 214, 'D', 'Una guía para el poblamiento del modelo geocelular', 0, 0),
(1034, 215, 'A', 'Viento', 1, 0),
(1035, 215, 'B', 'Oleaje', 0, 0),
(1036, 215, 'C', 'Glaciares', 0, 0),
(1037, 215, 'D', 'Escorrentía', 0, 0),
(1038, 216, 'A', 'FALSO', 0, 0),
(1039, 216, 'B', 'VERDADERO', 1, 0),
(1042, 217, 'A', 'Activas o Vivas', 0, 0),
(1043, 217, 'B', 'Directas o indirectas', 1, 0),
(1044, 217, 'C', 'Muertas o fósiles', 0, 0),
(1046, 218, 'A', 'Volcanes de lodo', 0, 0),
(1047, 218, 'B', 'Escape de gas', 0, 0),
(1048, 218, 'C', 'Chapopoteras', 1, 0),
(1049, 218, 'D', 'Derrame de aceite', 0, 0),
(1050, 219, 'A', 'Registros radioactivos', 0, 0),
(1051, 219, 'B', 'Registros resistivos', 0, 0),
(1052, 219, 'C', 'Registros de producción', 1, 0),
(1053, 219, 'D', 'Registros de densidad', 0, 0),
(1054, 220, 'A', ' FALSO', 1, 0),
(1055, 220, 'B', 'VERDADERO', 0, 0),
(1058, 221, 'A', 'Biogás', 0, 0),
(1059, 221, 'B', 'Hidratos de metano', 1, 0),
(1060, 221, 'C', 'Gas natural licuado', 0, 0),
(1061, 221, 'D', 'Dióxido de carbono', 0, 0),
(1062, 222, 'A', 'El Poiseuille', 0, 0),
(1063, 222, 'B', 'El Darcy', 0, 0),
(1064, 222, 'C', 'Pascal-segundo', 0, 0),
(1065, 222, 'D', 'El Centistoke', 1, 0),
(1066, 223, 'A', 'Permeabilidad', 1, 0),
(1067, 223, 'B', 'Porosidad', 0, 0),
(1068, 223, 'C', 'Capilaridad', 0, 0),
(1069, 223, 'D', 'Adsorción', 0, 0),
(1070, 224, 'A', 'Línea de contacto entre dos placas', 0, 0),
(1071, 224, 'B', 'Interrupción de la secuencia de depósito', 1, 0),
(1072, 224, 'C', 'Línea de contacto entre rocas recientes con antiguas', 0, 0),
(1073, 224, 'D', 'Interrupción por la falta de rocas no depositadas', 0, 0),
(1074, 225, 'A', 'Roca generadora', 0, 0),
(1075, 225, 'B', 'Roca sello', 0, 0),
(1076, 225, 'C', 'Roca almacenadora', 0, 0),
(1077, 225, 'D', 'Sistema Petrolero', 1, 0),
(1078, 226, 'A', 'Delimitación de areas', 0, 0),
(1079, 226, 'B', 'Planeación de proyectos', 0, 0),
(1080, 226, 'C', 'Caracterización estática', 0, 0),
(1081, 226, 'D', 'Evaluación de prospectos', 1, 0),
(1082, 227, 'A', 'Presión de poro', 1, 0),
(1083, 227, 'B', 'Presión del fluido', 0, 0),
(1084, 227, 'C', 'Presión de formación', 0, 0),
(1085, 227, 'D', 'Presión litostática', 0, 0),
(1086, 228, 'A', 'Modelo geo-celular ', 1, 0),
(1087, 228, 'B', 'Modelo geológico', 0, 0),
(1088, 228, 'C', 'Modelo dinámico', 0, 0),
(1089, 228, 'D', 'Modelo estático', 0, 0),
(1090, 229, 'A', 'Modelado estático', 1, 0),
(1091, 229, 'B', 'Edición de superficies', 0, 0),
(1092, 229, 'C', 'Interpretación estructural ', 0, 0),
(1093, 229, 'D', 'Modelo dinámico', 0, 0),
(1094, 230, 'A', 'Alquinos', 0, 0),
(1095, 230, 'B', 'Aromáticos', 0, 0),
(1096, 230, 'C', 'Alquenos', 0, 0),
(1097, 230, 'D', 'Alcanos', 1, 0),
(1098, 231, 'A', 'FALSO', 0, 0),
(1099, 231, 'B', 'VERDADERO', 1, 0),
(1102, 232, 'A', 'La porosidad y la permeabilidad', 1, 0),
(1103, 232, 'B', 'Saturación de agua y saturación de aceite', 0, 0),
(1104, 232, 'C', 'Contenido mineralógic', 0, 0),
(1105, 232, 'D', 'Volumen de arcilla', 0, 0),
(1106, 233, 'A', 'Geología Estructural', 0, 0),
(1107, 233, 'B', 'Paleontología', 0, 0),
(1108, 233, 'C', 'Estratigrafía', 0, 0),
(1109, 233, 'D', 'Geoquímica', 1, 0),
(1110, 234, 'A', 'Presión y Gravedad', 0, 0),
(1111, 234, 'B', 'Gravedad y Temperatura', 0, 0),
(1112, 234, 'C', 'Gravedad y Sedimentación', 0, 0),
(1113, 234, 'D', 'Presión y Temperatura', 1, 0),
(1114, 235, 'A', 'Cohesión', 0, 0),
(1115, 235, 'B', 'Dureza', 0, 0),
(1116, 235, 'C', 'Estructura interna', 1, 0),
(1117, 235, 'D', 'Conductibilidad ', 0, 0),
(1118, 236, 'A', 'FALSO', 1, 0),
(1119, 236, 'B', 'VERDADERO', 0, 0),
(1122, 237, 'A', 'Foraminíferos', 0, 0),
(1123, 237, 'B', 'Núcleos', 0, 0),
(1124, 237, 'C', 'Muestras de canal', 1, 0),
(1125, 237, 'D', 'Muestras de sedimentos', 0, 0),
(1126, 238, 'A', 'Hulla', 0, 0),
(1127, 238, 'B', 'Turba', 1, 0),
(1128, 238, 'C', 'Lignito', 0, 0),
(1129, 238, 'D', 'Antracita', 0, 0),
(1130, 239, 'A', 'Hulla', 0, 0),
(1131, 239, 'B', 'Turba', 0, 0),
(1132, 239, 'C', 'Lignito', 0, 0),
(1133, 239, 'D', 'Antracita', 1, 0),
(1134, 240, 'A', 'Gasómetro', 0, 0),
(1135, 240, 'B', 'Pirolisis', 0, 0),
(1136, 240, 'C', 'Espectrómetro', 0, 0),
(1137, 240, 'D', 'Cromatógrafo', 1, 0),
(1138, 241, 'A', '1.5 m/seg2', 0, 0),
(1139, 241, 'B', '6 radianes/seg,', 0, 0),
(1140, 241, 'C', '0.3 m/seg', 0, 0),
(1141, 241, 'D', '0.0027 m /seg2', 1, 0),
(1142, 242, 'A', 'Azimut', 1, 0),
(1143, 242, 'B', 'Offset máximo', 0, 0),
(1144, 242, 'C', 'Fold', 0, 0),
(1145, 242, 'D', 'Distancia entre detectores', 0, 0),
(1146, 243, 'A', 'Bitumen', 1, 0),
(1147, 243, 'B', 'Humina', 0, 0),
(1148, 243, 'C', 'Kerógeno', 0, 0),
(1149, 243, 'D', 'Carbono orgánico', 0, 0),
(1150, 244, 'A', 'Bioestratigrafía', 0, 0),
(1151, 244, 'B', 'Litoestratigrafía', 0, 0),
(1152, 244, 'C', 'Cronoestratigrafía', 1, 0),
(1153, 244, 'D', 'Estratigrafía', 0, 0),
(1154, 245, 'A', 'Oolitas y pellets', 0, 0),
(1155, 245, 'B', 'Aloquímicos y ortoquímicos', 1, 0),
(1156, 245, 'C', 'Radiolarios y briozarios', 0, 0),
(1157, 245, 'D', 'Intraclastos y litoclastos', 0, 0),
(1158, 246, 'A', 'Tectónica ignea', 0, 0),
(1159, 246, 'B', 'Neotectonica', 0, 0),
(1160, 246, 'C', 'Tectónica salina', 1, 0),
(1161, 246, 'D', 'Sintectonica', 0, 0),
(1162, 247, 'A', 'Registros SP', 0, 0),
(1163, 247, 'B', 'Registro RG', 0, 0),
(1164, 247, 'C', 'Registro Caliper', 0, 0),
(1165, 247, 'D', 'Registro de Densidad', 1, 0),
(1166, 248, 'A', 'Reservas probadas', 0, 0),
(1167, 248, 'B', 'Reservas probables', 1, 0),
(1168, 248, 'C', 'Reservas posibles', 0, 0),
(1169, 248, 'D', 'Reservas desarrolladas', 0, 0),
(1170, 249, 'A', 'reservas probadas', 0, 0),
(1171, 249, 'B', 'Reservas probables', 0, 0),
(1172, 249, 'C', 'Reservas posibles', 1, 0),
(1173, 249, 'D', 'Reservas desarrolladas', 0, 0),
(1174, 250, 'A', 'Mapas isopacos', 0, 0),
(1175, 250, 'B', 'Mapas estructurales', 0, 0),
(1176, 250, 'C', 'Mapas de litofacies', 1, 0),
(1177, 250, 'D', 'Mapas paleogeográficos', 0, 0),
(1178, 22, 'C', 'La resta de sus espectros de amplitud.', 0, 0),
(1179, 22, 'D', 'La división de sus espectros de amplitud.', 0, 0),
(1180, 36, 'C', 'Punto de reflejo Común.', 0, 0),
(1181, 36, 'D', 'Método Schlumberger', 0, 0),
(1182, 69, 'C', 'Foliación y birrefracción', 0, 0),
(1183, 69, 'D', 'Compresión y distención', 0, 0),
(1184, 74, 'C', 'Color, fracturamiento y edad.', 0, 0),
(1185, 74, 'D', 'Mineralogía y presión', 0, 0),
(1186, 112, 'C', 'Saturación de agua', 1, 0),
(1187, 112, 'D', 'Saturación de gas', 0, 0),
(1188, 161, 'C', 'Yacimientos de Gas Natural', 0, 0),
(1189, 161, 'D', 'Yacimientos de Gas y Condensado', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rondas`
--

CREATE TABLE `rondas` (
  `ID_RONDA` int(11) NOT NULL,
  `RONDA` varchar(255) DEFAULT '',
  `ID_ETAPA` int(2) NOT NULL,
  `ALIAS` varchar(128) DEFAULT NULL,
  `IS_DESEMPATE` tinyint(2) DEFAULT NULL,
  `CANTIDAD_PREGUNTAS` int(3) DEFAULT '0',
  `PREGUNTAS_POR_CATEGORIA` int(3) DEFAULT '0',
  `TURNOS_PREGUNTA_CONCURSANTE` int(3) DEFAULT '0',
  `SEGUNDOS_POR_PREGUNTA` int(3) DEFAULT '0',
  `SEGUNDOS_PASO` int(3) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `rondas`
--

INSERT INTO `rondas` (`ID_RONDA`, `RONDA`, `ID_ETAPA`, `ALIAS`, `IS_DESEMPATE`, `CANTIDAD_PREGUNTAS`, `PREGUNTAS_POR_CATEGORIA`, `TURNOS_PREGUNTA_CONCURSANTE`, `SEGUNDOS_POR_PREGUNTA`, `SEGUNDOS_PASO`) VALUES
(1, '1era Individual', 1, 'ind_primer_ronda', 0, 12, 4, 12, 16, 0),
(2, '2da Individual', 1, 'ind_segunda_ronda', 0, 12, 4, 12, 16, 0),
(3, 'Desempate Individual', 1, 'ind_desempate', 1, 4, 4, 4, 16, 0),
(4, '1era Grupal', 2, 'grp_primer_ronda', 0, 4, 4, 4, 16, 0),
(5, '2da Grupal', 2, 'grp_segunda_ronda', 0, 4, 4, 4, 16, 6),
(6, 'Desempate Grupal', 2, 'grp_desempate', 1, 4, 4, 4, 16, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rondas_log`
--

CREATE TABLE `rondas_log` (
  `ID_LOG` int(11) NOT NULL,
  `ID_RONDA` int(2) DEFAULT NULL,
  `ID_CATEGORIA` int(2) DEFAULT '0',
  `ID_CONCURSO` int(6) DEFAULT NULL,
  `INICIO` smallint(1) DEFAULT '0',
  `FIN` smallint(1) DEFAULT '0',
  `NIVEL_EMPATE` int(2) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `rondas_log`
--

INSERT INTO `rondas_log` (`ID_LOG`, `ID_RONDA`, `ID_CATEGORIA`, `ID_CONCURSO`, `INICIO`, `FIN`, `NIVEL_EMPATE`) VALUES
(1, 1, 1, 2, 1, 0, 0),
(2, 2, 1, 2, 1, 0, 0),
(3, 1, 1, 4, 1, 1, 0),
(4, 2, 1, 4, 1, 1, 0),
(5, 1, 1, 4, 1, 1, 0),
(6, 2, 1, 4, 1, 1, 0),
(7, 1, 3, 7, 1, 0, 0),
(8, 2, 3, 7, 1, 0, 0),
(9, 1, 3, 8, 1, 1, 0),
(10, 2, 3, 8, 1, 1, 0),
(11, 1, 3, 8, 1, 1, 0),
(12, 2, 3, 8, 1, 1, 0),
(13, 1, 1, 9, 1, 1, 0),
(14, 2, 1, 9, 1, 1, 0),
(15, 1, 2, 10, 1, 1, 0),
(16, 2, 2, 10, 1, 1, 0),
(17, 3, 2, 10, 1, 1, 1),
(18, 3, 2, 10, 1, 1, 2),
(19, 1, 1, 12, 1, 1, 0),
(20, 2, 1, 12, 1, 1, 0),
(21, 1, 1, 12, 1, 1, 0),
(22, 2, 1, 12, 1, 1, 0),
(23, 1, 3, 13, 1, 1, 0),
(24, 2, 3, 13, 1, 1, 0),
(25, 3, 3, 13, 1, 1, 1),
(26, 4, 4, 14, 1, 1, 0),
(27, 5, 4, 14, 1, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tablero_master`
--

CREATE TABLE `tablero_master` (
  `ID_TABLERO_MASTER` int(11) NOT NULL,
  `ID_CONCURSO` int(6) DEFAULT NULL,
  `CREADO_EN` datetime DEFAULT CURRENT_TIMESTAMP,
  `CERRADO` smallint(2) DEFAULT '0',
  `POSICIONES_GENERADAS` smallint(2) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tablero_master`
--

INSERT INTO `tablero_master` (`ID_TABLERO_MASTER`, `ID_CONCURSO`, `CREADO_EN`, `CERRADO`, `POSICIONES_GENERADAS`) VALUES
(1, 4, '2019-05-19 16:04:23', 0, 1),
(2, 8, '2019-05-19 16:51:35', 1, 1),
(3, 9, '2019-05-19 17:36:27', 1, 1),
(4, 10, '2019-05-19 17:57:08', 1, 1),
(5, 10, '2019-05-19 18:00:50', 1, 1),
(6, 10, '2019-05-19 18:03:48', 1, 1),
(7, 12, '2019-05-25 14:05:28', 1, 1),
(8, 13, '2019-05-25 14:14:08', 1, 1),
(9, 13, '2019-05-25 14:16:59', 1, 1),
(10, 14, '2019-05-25 14:32:16', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tablero_pasos`
--

CREATE TABLE `tablero_pasos` (
  `ID_TABLERO_PASO` int(11) NOT NULL,
  `ID_CONCURSO` int(6) NOT NULL,
  `ID_RONDA` int(2) NOT NULL,
  `ID_CONCURSANTE` int(6) DEFAULT NULL,
  `PREGUNTA_POSICION` int(2) NOT NULL,
  `PREGUNTA` int(6) NOT NULL,
  `RESPUESTA` int(6) DEFAULT NULL,
  `RESPUESTA_CORRECTA` tinyint(1) DEFAULT '0',
  `PUNTAJE` int(6) DEFAULT '0',
  `CONTESTADA` tinyint(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tablero_pasos`
--

INSERT INTO `tablero_pasos` (`ID_TABLERO_PASO`, `ID_CONCURSO`, `ID_RONDA`, `ID_CONCURSANTE`, `PREGUNTA_POSICION`, `PREGUNTA`, `RESPUESTA`, `RESPUESTA_CORRECTA`, `PUNTAJE`, `CONTESTADA`) VALUES
(1, 14, 5, 30, 1, 236, 1118, 1, 10, 1),
(2, 14, 5, 30, 2, 199, 970, 1, 10, 1),
(3, 14, 5, 29, 6, 237, 1124, 1, 10, 1),
(4, 14, 5, 30, 4, 205, 994, 0, -30, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tablero_posiciones`
--

CREATE TABLE `tablero_posiciones` (
  `ID_TABLERO_POSICION` int(11) NOT NULL,
  `ID_TABLERO_MASTER` int(6) DEFAULT NULL,
  `ID_CONCURSANTE` int(6) DEFAULT NULL,
  `POSICION` int(3) DEFAULT NULL,
  `PUNTAJE_TOTAL` int(6) DEFAULT NULL,
  `EMPATADO` smallint(2) DEFAULT NULL,
  `POSICION_CAMBIO` smallint(2) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tablero_posiciones`
--

INSERT INTO `tablero_posiciones` (`ID_TABLERO_POSICION`, `ID_TABLERO_MASTER`, `ID_CONCURSANTE`, `POSICION`, `PUNTAJE_TOTAL`, `EMPATADO`, `POSICION_CAMBIO`) VALUES
(1, 1, 12, 1, 1, 0, 0),
(2, 2, 16, 1, -4, 0, 0),
(3, 3, 17, 1, 6, 0, 0),
(4, 4, 24, 1, -1, 0, 0),
(5, 4, 23, 2, -2, 1, 1),
(6, 4, 19, 2, -2, 1, 1),
(7, 4, 20, 2, -2, 1, 1),
(8, 4, 18, 5, -3, 0, 0),
(9, 4, 21, 6, -5, 0, 0),
(10, 4, 22, 7, -5, 0, 0),
(11, 5, 20, 1, -1, 0, 0),
(12, 5, 23, 2, -9, 1, 1),
(13, 5, 19, 2, -9, 1, 1),
(14, 6, 23, 1, -1, 0, 0),
(15, 6, 19, 2, -5, 0, 0),
(16, 7, 26, 1, 20, 0, 0),
(17, 8, 27, 1, -50, 1, 1),
(18, 8, 28, 1, -50, 1, 1),
(19, 9, 27, 1, -50, 0, 0),
(20, 9, 28, 2, -90, 0, 0),
(21, 10, 30, 1, 50, 0, 0),
(22, 10, 29, 2, -50, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tablero_puntajes`
--

CREATE TABLE `tablero_puntajes` (
  `ID_TABLERO_PUNTAJE` int(11) NOT NULL,
  `ID_CONCURSO` int(6) NOT NULL,
  `ID_RONDA` int(2) NOT NULL,
  `ID_CONCURSANTE` int(6) DEFAULT NULL,
  `PREGUNTA_POSICION` int(2) NOT NULL,
  `PREGUNTA` int(6) NOT NULL,
  `RESPUESTA` int(6) DEFAULT NULL,
  `RESPUESTA_CORRECTA` tinyint(1) DEFAULT NULL,
  `PASO_PREGUNTA` tinyint(1) DEFAULT '0',
  `PUNTAJE` int(3) DEFAULT '0',
  `NIVEL_EMPATE` int(3) DEFAULT '0',
  `CONCURSANTE_PASO` int(11) DEFAULT NULL,
  `CONCURSANTE_TOMO` smallint(2) DEFAULT '0',
  `CONTESTADA` smallint(2) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tablero_puntajes`
--

INSERT INTO `tablero_puntajes` (`ID_TABLERO_PUNTAJE`, `ID_CONCURSO`, `ID_RONDA`, `ID_CONCURSANTE`, `PREGUNTA_POSICION`, `PREGUNTA`, `RESPUESTA`, `RESPUESTA_CORRECTA`, `PASO_PREGUNTA`, `PUNTAJE`, `NIVEL_EMPATE`, `CONCURSANTE_PASO`, `CONCURSANTE_TOMO`, `CONTESTADA`) VALUES
(1, 4, 1, 12, 1, 34, 163, 0, 0, 0, 0, NULL, 0, 0),
(2, 4, 1, 12, 2, 26, 132, 0, 0, 0, 0, NULL, 0, 0),
(3, 4, 1, 12, 3, 49, 216, 0, 0, 0, 0, NULL, 0, 0),
(4, 4, 1, 12, 4, 52, 228, 0, 0, 0, 0, NULL, 0, 0),
(5, 4, 2, 12, 1, 44, 201, 0, 0, -1, 0, NULL, 0, 0),
(6, 4, 2, 12, 2, 36, 171, 1, 0, 1, 0, NULL, 0, 0),
(7, 4, 2, 12, 3, 4, 52, 0, 0, -2, 0, NULL, 0, 0),
(8, 4, 2, 12, 4, 38, 179, 1, 0, 3, 0, NULL, 0, 0),
(9, 8, 1, 16, 1, 146, 770, 0, 0, 0, 0, NULL, 0, 0),
(10, 8, 1, 16, 2, 148, 778, 0, 0, 0, 0, NULL, 0, 0),
(11, 8, 1, 16, 3, 132, 723, 0, 0, 0, 0, NULL, 0, 0),
(12, 8, 1, 16, 4, 150, 789, 1, 0, 3, 0, NULL, 0, 0),
(13, 8, 2, 16, 1, 167, 852, 0, 0, -1, 0, NULL, 0, 0),
(14, 8, 2, 16, 2, 177, 890, 0, 0, -1, 0, NULL, 0, 0),
(15, 8, 2, 16, 3, 172, 870, 0, 0, -2, 0, NULL, 0, 0),
(16, 8, 2, 16, 4, 134, 732, 0, 0, -3, 0, NULL, 0, 0),
(17, 9, 1, 17, 1, 19, 111, 1, 0, 1, 0, NULL, 0, 0),
(18, 9, 1, 17, 2, 17, 102, 0, 0, 0, 0, NULL, 0, 0),
(19, 9, 1, 17, 3, 29, 142, 1, 0, 2, 0, NULL, 0, 0),
(20, 9, 1, 17, 4, 31, 152, 0, 0, 0, 0, NULL, 0, 0),
(21, 9, 2, 17, 1, 60, 252, 1, 0, 1, 0, NULL, 0, 0),
(22, 9, 2, 17, 2, 54, 237, 1, 0, 1, 0, NULL, 0, 0),
(23, 9, 2, 17, 3, 21, 114, 0, 0, -2, 0, NULL, 0, 0),
(24, 9, 2, 17, 4, 6, 60, 1, 0, 3, 0, NULL, 0, 0),
(25, 10, 1, 23, 1, 103, 416, 0, 0, 0, 0, NULL, 0, 0),
(26, 10, 1, 18, 1, 103, 416, 0, 0, 0, 0, NULL, 0, 0),
(27, 10, 1, 24, 1, 103, 417, 1, 0, 1, 0, NULL, 0, 0),
(28, 10, 1, 19, 1, 103, 418, 0, 0, 0, 0, NULL, 0, 0),
(29, 10, 1, 21, 1, 103, 418, 0, 0, 0, 0, NULL, 0, 0),
(30, 10, 1, 22, 1, 103, 419, 0, 0, 0, 0, NULL, 0, 0),
(31, 10, 1, 20, 1, 103, 419, 0, 0, 0, 0, NULL, 0, 0),
(32, 10, 1, 18, 2, 71, 293, 0, 0, 0, 0, NULL, 0, 0),
(33, 10, 1, 19, 2, 71, 295, 0, 0, 0, 0, NULL, 0, 0),
(34, 10, 1, 23, 2, 71, 292, 1, 0, 1, 0, NULL, 0, 0),
(35, 10, 1, 20, 2, 71, 292, 1, 0, 1, 0, NULL, 0, 0),
(36, 10, 1, 24, 2, 71, 293, 0, 0, 0, 0, NULL, 0, 0),
(37, 10, 1, 21, 2, 71, 294, 0, 0, 0, 0, NULL, 0, 0),
(38, 10, 1, 22, 2, 71, 295, 0, 0, 0, 0, NULL, 0, 0),
(39, 10, 1, 18, 3, 76, 312, 1, 0, 2, 0, NULL, 0, 0),
(40, 10, 1, 23, 3, 76, 310, 0, 0, 0, 0, NULL, 0, 0),
(41, 10, 1, 19, 3, 76, 311, 0, 0, 0, 0, NULL, 0, 0),
(42, 10, 1, 24, 3, 76, 311, 0, 0, 0, 0, NULL, 0, 0),
(43, 10, 1, 20, 3, 76, 312, 1, 0, 2, 0, NULL, 0, 0),
(44, 10, 1, 21, 3, 76, 312, 1, 0, 2, 0, NULL, 0, 0),
(45, 10, 1, 22, 3, 76, 313, 0, 0, 0, 0, NULL, 0, 0),
(46, 10, 1, 23, 4, 115, 452, 0, 0, 0, 0, NULL, 0, 0),
(47, 10, 1, 24, 4, 115, 453, 1, 0, 3, 0, NULL, 0, 0),
(48, 10, 1, 21, 4, 115, 454, 0, 0, 0, 0, NULL, 0, 0),
(49, 10, 1, 22, 4, 115, 455, 0, 0, 0, 0, NULL, 0, 0),
(50, 10, 1, 20, 4, 115, 455, 0, 0, 0, 0, NULL, 0, 0),
(51, 10, 1, 18, 4, 115, 452, 0, 0, 0, 0, NULL, 0, 0),
(52, 10, 1, 19, 4, 115, 453, 1, 0, 3, 0, NULL, 0, 0),
(53, 10, 2, 18, 1, 86, 352, 0, 0, -1, 0, NULL, 0, 0),
(54, 10, 2, 23, 1, 86, 350, 0, 0, -1, 0, NULL, 0, 0),
(55, 10, 2, 19, 1, 86, 351, 1, 0, 1, 0, NULL, 0, 0),
(56, 10, 2, 24, 1, 86, 351, 1, 0, 1, 0, NULL, 0, 0),
(57, 10, 2, 20, 1, 86, 353, 0, 0, -1, 0, NULL, 0, 0),
(58, 10, 2, 21, 1, 86, 352, 0, 0, -1, 0, NULL, 0, 0),
(59, 10, 2, 22, 1, 86, 353, 0, 0, -1, 0, NULL, 0, 0),
(60, 10, 2, 18, 2, 101, 411, 1, 0, 1, 0, NULL, 0, 0),
(61, 10, 2, 23, 2, 101, 408, 0, 0, -1, 0, NULL, 0, 0),
(62, 10, 2, 19, 2, 101, 409, 0, 0, -1, 0, NULL, 0, 0),
(63, 10, 2, 24, 2, 101, 409, 0, 0, -1, 0, NULL, 0, 0),
(64, 10, 2, 20, 2, 101, 411, 1, 0, 1, 0, NULL, 0, 0),
(65, 10, 2, 21, 2, 101, 410, 0, 0, -1, 0, NULL, 0, 0),
(66, 10, 2, 22, 2, 101, 411, 1, 0, 1, 0, NULL, 0, 0),
(67, 10, 2, 23, 3, 74, 304, 1, 0, 2, 0, NULL, 0, 0),
(68, 10, 2, 18, 3, 74, 305, 0, 0, -2, 0, NULL, 0, 0),
(69, 10, 2, 24, 3, 74, 305, 0, 0, -2, 0, NULL, 0, 0),
(70, 10, 2, 19, 3, 74, 305, 0, 0, -2, 0, NULL, 0, 0),
(71, 10, 2, 21, 3, 74, 1184, 0, 0, -2, 0, NULL, 0, 0),
(72, 10, 2, 20, 3, 74, 305, 0, 0, -2, 0, NULL, 0, 0),
(73, 10, 2, 22, 3, 74, 1185, 0, 0, -2, 0, NULL, 0, 0),
(74, 10, 2, 18, 4, 117, 461, 0, 0, -3, 0, NULL, 0, 0),
(75, 10, 2, 23, 4, 117, 460, 0, 0, -3, 0, NULL, 0, 0),
(76, 10, 2, 24, 4, 117, 461, 0, 0, -3, 0, NULL, 0, 0),
(77, 10, 2, 22, 4, 117, NULL, 0, 0, -3, 0, NULL, 0, 0),
(78, 10, 2, 20, 4, 117, NULL, 0, 0, -3, 0, NULL, 0, 0),
(79, 10, 2, 19, 4, 117, NULL, 0, 0, -3, 0, NULL, 0, 0),
(80, 10, 2, 21, 4, 117, NULL, 0, 0, -3, 0, NULL, 0, 0),
(81, 10, 3, 23, 1, 83, 338, 0, 0, -2, 1, NULL, 0, 0),
(82, 10, 3, 20, 1, 83, 339, 0, 0, -2, 1, NULL, 0, 0),
(83, 10, 3, 19, 1, 83, 339, 0, 0, -2, 1, NULL, 0, 0),
(84, 10, 3, 19, 2, 62, 261, 0, 0, -2, 1, NULL, 0, 0),
(85, 10, 3, 20, 2, 62, 258, 1, 0, 2, 1, NULL, 0, 0),
(86, 10, 3, 23, 2, 62, 259, 0, 0, -2, 1, NULL, 0, 0),
(87, 10, 3, 19, 3, 109, 434, 0, 0, -2, 1, NULL, 0, 0),
(88, 10, 3, 20, 3, 109, 435, 1, 0, 2, 1, NULL, 0, 0),
(89, 10, 3, 23, 3, 109, 434, 0, 0, -2, 1, NULL, 0, 0),
(90, 10, 3, 19, 4, 114, 450, 0, 0, -3, 1, NULL, 0, 0),
(91, 10, 3, 20, 4, 114, 451, 0, 0, -3, 1, NULL, 0, 0),
(92, 10, 3, 23, 4, 114, 448, 0, 0, -3, 1, NULL, 0, 0),
(93, 10, 3, 19, 5, 85, 349, 0, 0, -2, 2, NULL, 0, 0),
(94, 10, 3, 23, 5, 85, 346, 0, 0, -2, 2, NULL, 0, 0),
(95, 10, 3, 19, 6, 98, 400, 1, 0, 2, 2, NULL, 0, 0),
(96, 10, 3, 23, 6, 98, 400, 1, 0, 2, 2, NULL, 0, 0),
(97, 10, 3, 19, 7, 66, 275, 0, 0, -2, 2, NULL, 0, 0),
(98, 10, 3, 23, 7, 66, 276, 1, 0, 2, 2, NULL, 0, 0),
(99, 10, 3, 19, 8, 65, 271, 0, 0, -3, 2, NULL, 0, 0),
(100, 10, 3, 23, 8, 65, 271, 0, 0, -3, 2, NULL, 0, 0),
(101, 12, 1, 26, 1, 23, 121, 0, 0, 0, 0, NULL, 0, 0),
(102, 12, 1, 26, 2, 25, 127, 0, 0, 0, 0, NULL, 0, 0),
(103, 12, 1, 26, 3, 15, 96, 0, 0, 0, 0, NULL, 0, 0),
(104, 12, 1, 26, 4, 53, 233, 1, 0, 30, 0, NULL, 0, 0),
(105, 12, 2, 26, 1, 48, 215, 0, 0, -10, 0, NULL, 0, 0),
(106, 12, 2, 26, 2, 36, 170, 0, 0, -10, 0, NULL, 0, 0),
(107, 12, 2, 26, 3, 22, 1178, 0, 0, -20, 0, NULL, 0, 0),
(108, 12, 2, 26, 4, 31, 151, 1, 0, 30, 0, NULL, 0, 0),
(109, 13, 1, 27, 1, 167, 852, 0, 0, 0, 0, NULL, 0, 0),
(110, 13, 1, 28, 1, 167, 852, 0, 0, 0, 0, NULL, 0, 0),
(111, 13, 1, 27, 2, 146, 772, 0, 0, 0, 0, NULL, 0, 0),
(112, 13, 1, 28, 2, 146, 772, 0, 0, 0, 0, NULL, 0, 0),
(113, 13, 1, 27, 3, 141, 755, 0, 0, 0, 0, NULL, 0, 0),
(114, 13, 1, 28, 3, 141, 755, 0, 0, 0, 0, NULL, 0, 0),
(115, 13, 1, 27, 4, 153, 801, 0, 0, 0, 0, NULL, 0, 0),
(116, 13, 1, 28, 4, 153, 801, 0, 0, 0, 0, NULL, 0, 0),
(117, 13, 2, 27, 1, 143, 761, 1, 0, 10, 0, NULL, 0, 0),
(118, 13, 2, 28, 1, 143, 761, 1, 0, 10, 0, NULL, 0, 0),
(119, 13, 2, 27, 2, 161, 1188, 0, 0, -10, 0, NULL, 0, 0),
(120, 13, 2, 28, 2, 161, 1188, 0, 0, -10, 0, NULL, 0, 0),
(121, 13, 2, 28, 3, 159, 822, 0, 0, -20, 0, NULL, 0, 0),
(122, 13, 2, 27, 3, 159, 822, 0, 0, -20, 0, NULL, 0, 0),
(123, 13, 2, 27, 4, 130, 717, 0, 0, -30, 0, NULL, 0, 0),
(124, 13, 2, 28, 4, 130, 717, 0, 0, -30, 0, NULL, 0, 0),
(125, 13, 3, 27, 1, 149, 782, 1, 0, 20, 1, NULL, 0, 0),
(126, 13, 3, 28, 1, 149, 783, 0, 0, -20, 1, NULL, 0, 0),
(127, 13, 3, 28, 2, 172, 872, 0, 0, -20, 1, NULL, 0, 0),
(128, 13, 3, 27, 2, 172, 870, 0, 0, -20, 1, NULL, 0, 0),
(129, 13, 3, 27, 3, 174, 878, 0, 0, -20, 1, NULL, 0, 0),
(130, 13, 3, 28, 3, 174, 881, 0, 0, -20, 1, NULL, 0, 0),
(131, 13, 3, 27, 4, 160, 828, 0, 0, -30, 1, NULL, 0, 0),
(132, 13, 3, 28, 4, 160, 827, 0, 0, -30, 1, NULL, 0, 0),
(133, 14, 4, 29, 1, 209, 1012, 0, 0, 0, 0, NULL, 0, 0),
(134, 14, 4, 30, 1, 209, 1012, 0, 0, 0, 0, NULL, 0, 0),
(135, 14, 4, 29, 2, 183, 907, 1, 0, 10, 0, NULL, 0, 0),
(136, 14, 4, 30, 2, 183, 907, 1, 0, 10, 0, NULL, 0, 0),
(137, 14, 4, 29, 3, 238, 1128, 0, 0, 0, 0, NULL, 0, 0),
(138, 14, 4, 30, 3, 238, 1128, 0, 0, 0, 0, NULL, 0, 0),
(139, 14, 4, 29, 4, 230, 1094, 0, 0, 0, 0, NULL, 0, 0),
(140, 14, 4, 30, 4, 230, 1094, 0, 0, 0, 0, NULL, 0, 0),
(141, 14, 5, 29, 1, 236, 1119, 0, 2, -10, 0, 30, 1, 1),
(142, 14, 5, 30, 5, 228, 1086, 1, 0, 10, 0, NULL, 0, 1),
(143, 14, 5, 29, 2, 199, 971, 0, 2, -10, 0, 30, 1, 1),
(144, 14, 5, 30, 6, 237, 1123, 0, 2, -10, 0, 29, 1, 1),
(145, 14, 5, 29, 3, 248, 1169, 0, 2, -20, 0, 30, 0, 1),
(146, 14, 5, 30, 7, 188, 929, 1, 0, 20, 0, NULL, 0, 1),
(147, 14, 5, 29, 4, 205, 996, 0, 2, -30, 0, 30, 1, 1),
(148, 14, 5, 30, 8, 202, 983, 1, 0, 30, 0, NULL, 0, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`ID_CATEGORIA`);

--
-- Indices de la tabla `categorias_etapa`
--
ALTER TABLE `categorias_etapa`
  ADD PRIMARY KEY (`ID_CAT_ETAPA`),
  ADD KEY `fk_catEtapa` (`ID_CATEGORIA`),
  ADD KEY `fk_etapaCat` (`ID_ETAPA`);

--
-- Indices de la tabla `concursantes`
--
ALTER TABLE `concursantes`
  ADD PRIMARY KEY (`ID_CONCURSANTE`),
  ADD KEY `FK_concursoConcursante` (`ID_CONCURSO`);

--
-- Indices de la tabla `concursos`
--
ALTER TABLE `concursos`
  ADD PRIMARY KEY (`ID_CONCURSO`),
  ADD KEY `FK_etapaConcurso` (`ID_ETAPA`),
  ADD KEY `ID_RONDA` (`ID_RONDA`),
  ADD KEY `fkConcursoCategoria` (`ID_CATEGORIA`);

--
-- Indices de la tabla `etapas_tipo_concurso`
--
ALTER TABLE `etapas_tipo_concurso`
  ADD PRIMARY KEY (`ID_ETAPA`);

--
-- Indices de la tabla `grados_dificultad`
--
ALTER TABLE `grados_dificultad`
  ADD PRIMARY KEY (`ID_GRADO`);

--
-- Indices de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD PRIMARY KEY (`ID_PREGUNTA`),
  ADD KEY `FK_gradodificultadPregunta` (`ID_GRADO`),
  ADD KEY `FK_categoriaPregunta` (`ID_CATEGORIA`);

--
-- Indices de la tabla `preguntas_generadas`
--
ALTER TABLE `preguntas_generadas`
  ADD PRIMARY KEY (`ID_GENERADA`),
  ADD KEY `FK_preguntaGenerada` (`ID_PREGUNTA`),
  ADD KEY `FK_concursoGenerada` (`ID_CONCURSO`),
  ADD KEY `FK_rondaGenerada` (`ID_RONDA`),
  ADD KEY `fk_pgConcursante` (`ID_CONCURSANTE`);

--
-- Indices de la tabla `reglas`
--
ALTER TABLE `reglas`
  ADD PRIMARY KEY (`ID_REGLA`),
  ADD KEY `FK_rondaRegla` (`ID_RONDA`);

--
-- Indices de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  ADD PRIMARY KEY (`ID_RESPUESTA`),
  ADD KEY `FK_preguntaRespuesta` (`ID_PREGUNTA`);

--
-- Indices de la tabla `rondas`
--
ALTER TABLE `rondas`
  ADD PRIMARY KEY (`ID_RONDA`),
  ADD KEY `FK_etapaRonda` (`ID_ETAPA`);

--
-- Indices de la tabla `rondas_log`
--
ALTER TABLE `rondas_log`
  ADD PRIMARY KEY (`ID_LOG`),
  ADD KEY `FK_logRonda` (`ID_RONDA`),
  ADD KEY `FK_logConcurso` (`ID_CONCURSO`),
  ADD KEY `fklogCategoria` (`ID_CATEGORIA`);

--
-- Indices de la tabla `tablero_master`
--
ALTER TABLE `tablero_master`
  ADD PRIMARY KEY (`ID_TABLERO_MASTER`),
  ADD KEY `fkTpConcurso` (`ID_CONCURSO`);

--
-- Indices de la tabla `tablero_pasos`
--
ALTER TABLE `tablero_pasos`
  ADD PRIMARY KEY (`ID_TABLERO_PASO`),
  ADD KEY `FK_concursoPaso` (`ID_CONCURSO`),
  ADD KEY `FK_rondaPaso` (`ID_RONDA`),
  ADD KEY `FK_preguntaPaso` (`PREGUNTA`),
  ADD KEY `FK_respuestaPaso` (`RESPUESTA`),
  ADD KEY `ID_CONCURSANTE` (`ID_CONCURSANTE`);

--
-- Indices de la tabla `tablero_posiciones`
--
ALTER TABLE `tablero_posiciones`
  ADD PRIMARY KEY (`ID_TABLERO_POSICION`),
  ADD KEY `fkTpConcursante` (`ID_CONCURSANTE`),
  ADD KEY `ID_TABLERO_MASTER` (`ID_TABLERO_MASTER`);

--
-- Indices de la tabla `tablero_puntajes`
--
ALTER TABLE `tablero_puntajes`
  ADD PRIMARY KEY (`ID_TABLERO_PUNTAJE`),
  ADD KEY `FK_concursoTablero` (`ID_CONCURSO`),
  ADD KEY `FK_rondaTablero` (`ID_RONDA`),
  ADD KEY `FK_preguntaTablero` (`PREGUNTA`),
  ADD KEY `FK_respuestaTablero` (`RESPUESTA`),
  ADD KEY `ID_CONCURSANTE` (`ID_CONCURSANTE`),
  ADD KEY `FK_concursantePaso` (`CONCURSANTE_PASO`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `ID_CATEGORIA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `categorias_etapa`
--
ALTER TABLE `categorias_etapa`
  MODIFY `ID_CAT_ETAPA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `concursantes`
--
ALTER TABLE `concursantes`
  MODIFY `ID_CONCURSANTE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `concursos`
--
ALTER TABLE `concursos`
  MODIFY `ID_CONCURSO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `etapas_tipo_concurso`
--
ALTER TABLE `etapas_tipo_concurso`
  MODIFY `ID_ETAPA` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `grados_dificultad`
--
ALTER TABLE `grados_dificultad`
  MODIFY `ID_GRADO` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  MODIFY `ID_PREGUNTA` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;

--
-- AUTO_INCREMENT de la tabla `preguntas_generadas`
--
ALTER TABLE `preguntas_generadas`
  MODIFY `ID_GENERADA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT de la tabla `reglas`
--
ALTER TABLE `reglas`
  MODIFY `ID_REGLA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  MODIFY `ID_RESPUESTA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1190;

--
-- AUTO_INCREMENT de la tabla `rondas`
--
ALTER TABLE `rondas`
  MODIFY `ID_RONDA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `rondas_log`
--
ALTER TABLE `rondas_log`
  MODIFY `ID_LOG` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `tablero_master`
--
ALTER TABLE `tablero_master`
  MODIFY `ID_TABLERO_MASTER` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tablero_pasos`
--
ALTER TABLE `tablero_pasos`
  MODIFY `ID_TABLERO_PASO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tablero_posiciones`
--
ALTER TABLE `tablero_posiciones`
  MODIFY `ID_TABLERO_POSICION` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `tablero_puntajes`
--
ALTER TABLE `tablero_puntajes`
  MODIFY `ID_TABLERO_PUNTAJE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `categorias_etapa`
--
ALTER TABLE `categorias_etapa`
  ADD CONSTRAINT `fk_catEtapa` FOREIGN KEY (`ID_CATEGORIA`) REFERENCES `categorias` (`ID_CATEGORIA`),
  ADD CONSTRAINT `fk_etapaCat` FOREIGN KEY (`ID_ETAPA`) REFERENCES `etapas_tipo_concurso` (`ID_ETAPA`);

--
-- Filtros para la tabla `concursantes`
--
ALTER TABLE `concursantes`
  ADD CONSTRAINT `FK_concursoConcursante` FOREIGN KEY (`ID_CONCURSO`) REFERENCES `concursos` (`ID_CONCURSO`);

--
-- Filtros para la tabla `concursos`
--
ALTER TABLE `concursos`
  ADD CONSTRAINT `FK_etapaConcurso` FOREIGN KEY (`ID_ETAPA`) REFERENCES `etapas_tipo_concurso` (`ID_ETAPA`),
  ADD CONSTRAINT `concursos_ibfk_1` FOREIGN KEY (`ID_RONDA`) REFERENCES `rondas` (`ID_RONDA`),
  ADD CONSTRAINT `fkConcursoCategoria` FOREIGN KEY (`ID_CATEGORIA`) REFERENCES `categorias` (`ID_CATEGORIA`);

--
-- Filtros para la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD CONSTRAINT `FK_categoriaPregunta` FOREIGN KEY (`ID_CATEGORIA`) REFERENCES `categorias` (`ID_CATEGORIA`),
  ADD CONSTRAINT `FK_gradodificultadPregunta` FOREIGN KEY (`ID_GRADO`) REFERENCES `grados_dificultad` (`ID_GRADO`);

--
-- Filtros para la tabla `preguntas_generadas`
--
ALTER TABLE `preguntas_generadas`
  ADD CONSTRAINT `FK_concursoGenerada` FOREIGN KEY (`ID_CONCURSO`) REFERENCES `concursos` (`ID_CONCURSO`),
  ADD CONSTRAINT `FK_preguntaGenerada` FOREIGN KEY (`ID_PREGUNTA`) REFERENCES `preguntas` (`ID_PREGUNTA`),
  ADD CONSTRAINT `FK_rondaGenerada` FOREIGN KEY (`ID_RONDA`) REFERENCES `rondas` (`ID_RONDA`),
  ADD CONSTRAINT `fk_pgConcursante` FOREIGN KEY (`ID_CONCURSANTE`) REFERENCES `concursantes` (`ID_CONCURSANTE`);

--
-- Filtros para la tabla `reglas`
--
ALTER TABLE `reglas`
  ADD CONSTRAINT `FK_rondaRegla` FOREIGN KEY (`ID_RONDA`) REFERENCES `rondas` (`ID_RONDA`);

--
-- Filtros para la tabla `respuestas`
--
ALTER TABLE `respuestas`
  ADD CONSTRAINT `FK_preguntaRespuesta` FOREIGN KEY (`ID_PREGUNTA`) REFERENCES `preguntas` (`ID_PREGUNTA`);

--
-- Filtros para la tabla `rondas`
--
ALTER TABLE `rondas`
  ADD CONSTRAINT `FK_etapaRonda` FOREIGN KEY (`ID_ETAPA`) REFERENCES `etapas_tipo_concurso` (`ID_ETAPA`);

--
-- Filtros para la tabla `rondas_log`
--
ALTER TABLE `rondas_log`
  ADD CONSTRAINT `FK_logConcurso` FOREIGN KEY (`ID_CONCURSO`) REFERENCES `concursos` (`ID_CONCURSO`),
  ADD CONSTRAINT `FK_logRonda` FOREIGN KEY (`ID_RONDA`) REFERENCES `rondas` (`ID_RONDA`),
  ADD CONSTRAINT `fklogCategoria` FOREIGN KEY (`ID_CATEGORIA`) REFERENCES `categorias` (`ID_CATEGORIA`);

--
-- Filtros para la tabla `tablero_master`
--
ALTER TABLE `tablero_master`
  ADD CONSTRAINT `fkTpConcurso` FOREIGN KEY (`ID_CONCURSO`) REFERENCES `concursos` (`ID_CONCURSO`);

--
-- Filtros para la tabla `tablero_pasos`
--
ALTER TABLE `tablero_pasos`
  ADD CONSTRAINT `FK_concursoPaso` FOREIGN KEY (`ID_CONCURSO`) REFERENCES `concursos` (`ID_CONCURSO`),
  ADD CONSTRAINT `FK_pasoConcursante` FOREIGN KEY (`ID_CONCURSANTE`) REFERENCES `concursantes` (`ID_CONCURSANTE`),
  ADD CONSTRAINT `FK_preguntaPaso` FOREIGN KEY (`PREGUNTA`) REFERENCES `preguntas` (`ID_PREGUNTA`),
  ADD CONSTRAINT `FK_respuestaPaso` FOREIGN KEY (`RESPUESTA`) REFERENCES `respuestas` (`ID_RESPUESTA`),
  ADD CONSTRAINT `FK_rondaPaso` FOREIGN KEY (`ID_RONDA`) REFERENCES `rondas` (`ID_RONDA`);

--
-- Filtros para la tabla `tablero_posiciones`
--
ALTER TABLE `tablero_posiciones`
  ADD CONSTRAINT `fkTpConcursante` FOREIGN KEY (`ID_CONCURSANTE`) REFERENCES `concursantes` (`ID_CONCURSANTE`),
  ADD CONSTRAINT `tablero_posiciones_ibfk_1` FOREIGN KEY (`ID_TABLERO_MASTER`) REFERENCES `tablero_master` (`ID_TABLERO_MASTER`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tablero_puntajes`
--
ALTER TABLE `tablero_puntajes`
  ADD CONSTRAINT `FK_concursantePaso` FOREIGN KEY (`CONCURSANTE_PASO`) REFERENCES `concursantes` (`ID_CONCURSANTE`),
  ADD CONSTRAINT `FK_concursoTablero` FOREIGN KEY (`ID_CONCURSO`) REFERENCES `concursos` (`ID_CONCURSO`),
  ADD CONSTRAINT `FK_preguntaTablero` FOREIGN KEY (`PREGUNTA`) REFERENCES `preguntas` (`ID_PREGUNTA`),
  ADD CONSTRAINT `FK_respuestaTablero` FOREIGN KEY (`RESPUESTA`) REFERENCES `respuestas` (`ID_RESPUESTA`),
  ADD CONSTRAINT `FK_rondaTablero` FOREIGN KEY (`ID_RONDA`) REFERENCES `rondas` (`ID_RONDA`),
  ADD CONSTRAINT `tablero_puntajes_ibfk_1` FOREIGN KEY (`ID_CONCURSANTE`) REFERENCES `concursantes` (`ID_CONCURSANTE`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
