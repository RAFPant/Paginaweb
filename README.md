# Sistema de Encuestas

Este repositorio contiene la aplicación de encuestas con interfaz web y una API ligera para clientes (móvil/SPA). A continuación tienes la documentación principal y la descripción por secciones, tal como la solicitaste.

✅ 1. ARCHIVOS DE SESIÓN Y CONFIGURACIÓN

📌 `config/conexion.php`

- Propósito: conexión MySQL mediante `mysqli`.
- Devuelve la variable `$conn` usada por la mayoría de los scripts PHP.
- Recomendación: mover credenciales a `.env` y usar `password_hash()` para contraseñas.

📌 `login.php`

- Formulario de acceso y lógica de inicio de sesión.
- Verifica usuario + password (MD5 en el repositorio actual).
- Crea `$_SESSION['id']` y `$_SESSION['rol']` y redirige a `index_admin.php` o `index_encuestador.php`.

📌 `logout.php`

- Cierra la sesión y redirige a `login.php`.

---

✅ 2. PANEL DEL ADMINISTRADOR

📌 `index_admin.php`

Funciones principales:
- Crear preguntas oficiales.
- Editar preguntas oficiales.
- Eliminar preguntas oficiales.
- Ver listado completo de preguntas.
- Exportar preguntas y respuestas (PDF/CSV).
- Buscar productor.

Acceso a:
- Gestión de dimensiones.
- Banco de preguntas precargadas.
- Creación de preguntas desde el banco.

Funciones administrativas adicionales:
- Crear cuentas de encuestadores (exclusivo admin).

Botones / accesos principales:
- ➕ Crear pregunta  •  📚 Dimensiones  •  🧩 Banco de preguntas precargadas
- 🏗️ Crear pregunta desde banco  •  👤 Crear encuestador  •  🔎 Buscar por dimensiones
- 🔍 Buscar productor  •  📄 Exportar  •  🚪 Cerrar sesión

---

✅ 3. PANEL DEL ENCUESTADOR

📌 `index_encuestador.php`

Funciones:
- Crear, editar y eliminar preguntas oficiales.
- Listado de preguntas con opciones y dimensiones.
- Exportar resultados.
- Buscar productor.

Acceso a:
- Crear pregunta usando preguntas precargadas.
- Ver preguntas filtradas por dimensión.

Nota: botones y acciones paralelas al admin excepto las funciones exclusivas de administración (gestión de encuestadores, etc.).

---

✅ 4. DIMENSIONES


📌 `dimensiones_admin.php`
- Listado completo de dimensiones; crear/editar/eliminar dimensiones.
- `agregar.php`, `editar.php`, `actualizar.php`, `eliminar.php` — CRUD completo para dimensiones.
- Nota: si la BD está configurada con ON DELETE CASCADE, eliminar una dimensión también borra preguntas relacionadas.

---

✅ 5. PREGUNTAS OFICIALES

📁 `/preguntas/`

📌 `agregar.php` — Inserta una nueva pregunta oficial; si es `cerrada`, guarda sus opciones y asigna la dimensión.
📌 `editar.php` — Muestra formulario para editar la pregunta.
📌 `actualizar.php` — Actualiza texto, tipo, dimensión; reemplaza opciones si aplica.
📌 `eliminar.php` — Elimina la pregunta y sus opciones.

---

✅ 6. BANCO DE PREGUNTAS PRECARGADAS

📌 `preguntas_precargadas_admin.php` — Admin del banco de preguntas precargadas.
- Funciones: agregar, listar, eliminar preguntas precargadas y asignación de `id_dimension`.
- `obtener_precargadas.php` — Endpoint para obtener preguntas precargadas filtradas por dimensión en JSON (usado por `creacion_desde_banco.php`).

---

✅ 7. CREACIÓN DESDE BANCO

📌 `creacion_desde_banco.php`
- Usada por Admin y Encuestador para crear preguntas oficiales desde el banco de precargadas.
- Selecciona dimensión, carga precargadas y crea la pregunta oficial con tipo (abierta/cerrada) y posibles opciones.

---

✅ 8. EXPORTACIONES

📁 `/exportar/`

📌 `exportar_preguntas.php` — Exporta preguntas oficiales a PDF (usa FPDF).
📌 `exportar_respuestas.php` — Exporta respuestas capturadas a CSV.

---

✅ 9. BÚSQUEDAS

📌 `buscar_productor.php` — Buscar y gestionar productores y sus encuestas; exportar desde listado.
📌 `preguntas/buscar_por_dimension.php` — Ver preguntas por dimensión.

---

✅ 10. CUENTAS DE ENCUESTADORES

📌 `crear_encuestador.php` — (Funciones en `index_admin.php`) Form para crear encuestador.
- Validaciones: comprobar usuario no repetido.
- Guardar password (actualmente MD5; se recomienda `password_hash()` y `password_verify()`).

---

## API (carpeta `encuestas_api/`) — documentación y endpoints
- `obtener_preguntas.php` — GET: devuelve preguntas activas con opciones.
- `subir_encuesta.php` — POST: crea productor, encuesta y registra respuestas.
- `subir_respuestas.php` — POST: inserta respuestas en lote.
- `encuesta_detalle.php` — GET: devuelve detalle de encuesta + productor + respuestas.
- `estadisticas.php` — GET: devuelve estadísticas en formato JSON (totales, por día, etc.).

---

## Archivos y utilidades de configuración
- `config/conexion.php` — conexión mysqli usada por la app y la mayor parte de scripts.
- `encuestas_api/config/conexion.php` — conexión para los endpoints de la API (variable `$conexion`).

---

## Advertencias, recomendaciones y faltantes del codigo
- El proyecto actualmente almacena contraseñas usando MD5. Si agregas usuarios manualmente en la base de datos, usa MD5 para mantener compatibilidad con el login actual.
- No se a comprobado el subir encuestas de la app mobil ni se a hecho una funcion para ver las encuestas de manera local (general) solo una ver al productor


## Pasos para integrar en docker el proyecto (no comprobado por falta de tiempo ya que se pidio a 2 dias de terminar no se pudo instalar en mi maquina)


## Crear carpeta del proyecto

Crea una carpeta nueva (puede estar donde quieras):

paginaweb-docker


## Estructura mínima

- Dentro de paginaweb-docker crea esto:

- paginaweb-docker/
    │
    ├── docker-compose.yml
    │
    ├──  web/
    │   ├── Dockerfile
    │   └── src/
    │       └── (poner en este apartado el proyecto)
    │
    └── db/
        └── init.sql


## Copiar la pagina web

- coloca el contenido del paginaweb.zip ya sea si esta en descargas o si lo tiene ejecutando de manera local como yo solo para ver o probar el proyecto

- C:\xampp\htdocs\encuestas

- y pégalo dentro de:

- paginaweb-docker/web/src/

- No modifiques archivos, solo copia.


## Crear docker-compose.yml

- paginaweb-docker/docker-compose.yml

- version: "3.8"

- services:
-  web:
-    build: ./web
-    ports:
-      - "8080:80"
-    volumes:
-      - ./web/src:/var/www/html
-    depends_on:
-      - db

-  db:
-    image: mariadb:10.4
-    environment:
-      MYSQL_ROOT_PASSWORD: root
-      MYSQL_DATABASE: encuesta_db
-    volumes:
-      - db_data:/var/lib/mysql
-      - ./db/init.sql:/docker-entrypoint-initdb.d/init.sql

- volumes:
-  db_data:


## Crear Dockerfile

- paginaweb-docker/web/Dockerfile

- FROM php:8.2-apache

- RUN docker-php-ext-install mysqli pdo pdo_mysql
- RUN a2enmod rewrite

- WORKDIR /var/www/html


##  Crear archivo SQL

-   paginaweb-docker/db/init.sql

-   Pega TODO el SQL de tu base de datos
(el dump completo que ya tienes).


## Ajustar conexión a la BD (OBLIGATORIO)
- En TODOS tus archivos conexion.php cambia:

- $host = "localhost";

- por:

- $host = "db";

- No cambies nada más.


## Levantar los contenedores

- Abre terminal en la carpeta paginaweb-docker y ejecuta:

- docker-compose up --build


## Abrir el sistema

- En el navegador entra a:

- http://localhost:8080


- Para detener

- CTRL + C

- o en otra terminal:

- docker-compose down


- Para volver a levantar

- docker-compose up


 Resultado final

✔ Página web corriendo en contenedor  
✔ Base de datos en contenedor  
✔ Datos persistentes  




