Visualizar en tiempo real y de forma histórica la temperatura capturada por sensores ubicados en diferentes lugares, a través de una interfaz gráfica (Dashboard).

Materia: Cómputo en la Niebla (6to Semestre) 

Docente: Jorge Sandoval Leal 

Proyecto: Dashboard Web de Monitoreo de Temperatura 

Objetivo Principal: Visualizar en tiempo real y de forma histórica la temperatura capturada por sensores ubicados en diferentes lugares, a través de una interfaz gráfica (Dashboard) limpia, interactiva y moderna.

El sistema es una aplicación web responsiva diseñada para leer métricas de temperatura almacenadas en una base de datos MySQL. Cuenta con dos modos principales de visualización:

Modo Tiempo Real: Realiza consultas automáticas cada segundo para mantener la gráfica actualizada con los últimos datos registrados, sin necesidad de recargar la página.

Modo Histórico: Permite seleccionar un rango de fechas para analizar tendencias de temperatura en el pasado. Los datos se agrupan de forma por minuto, por hora o por día dependiendo de la amplitud del rango seleccionado para mantener un rendimiento óptimo.

Herramientas y Tecnologías Utilizadas

El proyecto fue desarrollado utilizando un stack tecnológico, separando claramente el backend (servidor) del frontend (cliente):

Backend & Base de Datos

PHP: Actúa como el motor del lado del servidor. Se utilizó para crear APIs REST encargadas de conectarse a la base de datos, ejecutar consultas complejas, procesar la información y devolverla al cliente en formato JSON.

MySQL: Sistema de gestión de base de datos relacional utilizado para almacenar todo el historial de las temperaturas capturadas por los sensores de cada lugar.

Python: Utilizado para crear un script de simulación (mock.py). Este script inyecta datos ficticios pero realistas de temperatura directamente en la base de datos MySQL, permitiendo probar la aplicación de forma integral.

Frontend

HTML: Proporciona la estructura semántica y el esqueleto básico del dashboard.

Tailwind CSS: Framework de CSS basado en utilidades. Se utilizó para diseñar y estilizar la interfaz, dándole un diseño moderno con modo oscuro, responsivo y con componentes visuales reactivos (hovers, animaciones).

JavaScript: Maneja la lógica de la interfaz y establece la mediación dinámica entre la vista y las APIs.

jQuery: Librería de JavaScript utilizada específicamente para simplificar las peticiones asíncronas HTTP (AJAX) hacia las APIs de PHP y facilitar la manipulación del DOM. Todo está servido desde un archivo de manera local para operar sin internet.

ApexCharts: Librería de gráficos interactivos de alto rendimiento. Se empleó para renderizar las variaciones de temperatura en una gráfica de tipo área, que soporta zoom, paneo y animaciones al recibir nueva información en tiempo real.

Estructura y Función de los Archivos

Directorio Principal (/)

inicio.php: El archivo principal de la interfaz web. Contiene todo el código HTML, los estilos de diseño base e incluye las llamadas a los archivos JavaScript estáticos. Posee un panel superior con estadísticas clave y un área principal donde se muestra el gráfico interactivo.

mock.py: Script de Python que genera e inserta registros de temperaturas falsos a la base de datos, simulando el funcionamiento continuo de los sensores de hardware reales.

output.css: Archivo autogenerado por Tailwind CSS que contiene únicamente las clases estilos utilitarios que finalmente se emplean de manera real en inicio.php

Directorio de Configuración (/config/)

database.php: Archivo central donde se administran las credenciales de la base de datos. Se encarga de instanciar la conexión MySQL, fijar el formato de caracteres (UTF-8) y contiene configuraciones globales compartidas como las IDs de los lugares monitorizados.
Directorio de APIs (/api/)

realtime.php: Diseñado para servir la información más reciente de la temperatura. Cuenta con una lógica de consulta para la carga inicial y el envío de diferencias en las peticiones.

historic.php: Acepta parámetros por URL de rango de "inicio" y "fin" (fechas) y ejecuta consultas complejas de agregación, retorna promedios por minuto, hora o día en formato JSON apto para la gráfica.
Directorio Fuente (/src/)

chart.js: Inicializa la configuración de ApexCharts, contiene la lógica asíncrona de comunicación (AJAX) para los datos en tiempo real, gestiona las consultas del historial y orquesta la visibilidad entre interfaces (botones y tooltips).

data.php: Script encargado de las consultas globales resumen (Cantidad de datos procesados, temperatura histórica promedio y picos máximos), resultados que inyecta en la primera carga directamente en inicio.php.

jquery-3.7.1.min.js: Librería local de la dependencia jQuery, para no depender de internet al ejecutar la aplicación local.

apexcharts.min.js: Librería local para construir los planos analíticos dinámicos sin internet.

Directorio de Imágenes (/images/)

Aloja los recusos gráficos multimedia fijos del sitio web, como el favicon de la pestaña.
