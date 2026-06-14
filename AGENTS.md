# AGENTS.md — Ikasgela

## Descripción del proyecto

**Ikasgela** es una plataforma de gestión del aprendizaje (LMS) construida con **Laravel 12** y **PHP 8.4**. Permite a profesores crear cursos con unidades y actividades, asignarlas a alumnos, hacer seguimiento del progreso, evaluar con rúbricas, y gestionar recursos educativos variados.

### Stack tecnológico

- **Backend**: Laravel 12, PHP 8.4
- **Frontend**: Livewire 3, Bootstrap/SASS, Vite
- **Base de datos**: MySQL (con soporte para cache de Eloquent vía `ymigval/laravel-model-cache`)
- **Tiempo real**: Laravel Reverb (WebSockets)
- **Storage**: AWS S3 compatible (via Flysystem)
- **Testing**: PHPUnit 12, Paratest (ejecución paralela), Laravel Dusk (browser)
- **Refactoring**: Rector (configurado para Laravel 12)

---

## Arquitectura del dominio

### Roles de usuario

| Rol | Descripción |
|---|---|
| `admin` | Gestiona la plataforma: organizaciones, periodos, categorías, roles |
| `profesor` | Crea y gestiona cursos, unidades, actividades; evalúa a los alumnos |
| `tutor` | Supervisa grupos de alumnos |
| `alumno` | Realiza las actividades asignadas |

### Entidades principales

- **Organization / Period / Category** — estructura organizativa de nivel superior
- **Curso → Unidad → Actividad** — jerarquía del contenido educativo
- **Tarea** — pivot entre `User` (alumno) y `Actividad`; registra el estado de cada actividad para cada alumno
- **Registro** — log de cada cambio de estado de una `Tarea` (historial de actividad)

### Estados de una Tarea

```
10 → Nueva          20 → Aceptada       30 → Enviada
31 → Reiniciada     40 → Revisada: OK   41 → Revisada: Error
42 → Avance auto    50 → Terminada      60 → Archivada
62 → Archivada comp 64 → Enviada arch
```

### Tipos de recursos de una Actividad

`IntellijProject`, `YoutubeVideo`, `MarkdownText`, `Cuestionario`, `FileUpload`, `FileResource`, `Feedback`, `LinkCollection`, `Selector`, `Rubric`, `FlashDeck`, `TestResult`

Todos son clonables junto con la actividad vía `bkwld/cloner`.

---

## Comandos clave

> ⚠️ **Los tests se ejecutan siempre dentro del contenedor Docker** (`ikasgela-laravel-1`).
> Lo más cómodo es usar el `Makefile` de `despliegue/dev/` desde fuera del contenedor,
> o ejecutar directamente en el contenedor con `docker compose exec laravel <cmd>`.

```bash
# --- Desde despliegue/dev/ (recomendado) ---

make test           # migrate:fresh --env=testing + paratest (suite completa, ~20 s)
make coverage       # igual que test pero genera informe HTML en ikasgela/coverage/
make dusk           # migrate:fresh --seed + tests Dusk (browser)
make assets         # compilar JS para entorno DEV (ver sección Vite más abajo)

# --- Dentro del contenedor ---

# Instalar dependencias PHP
composer install

# Compilar assets (NO usar npm run build directamente en dev — ver Vite)
# Usar make assets desde despliegue/dev/

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Limpiar caché de aplicación
php artisan cache:clear && php artisan config:clear && php artisan view:clear

# Ejecutar tests con paratest (paralelo, ~20 s para la suite completa)
php vendor/bin/paratest --colors --stop-on-failure

# Ejecutar tests con cobertura XML
php vendor/bin/paratest --colors --stop-on-failure \
  --coverage-xml coverage-xml --coverage-filter app

# Ejecutar tests de un directorio concreto
php vendor/bin/phpunit tests/Feature/Estructura/

# Actualizar helpers de IDE (tras cambios en modelos)
php artisan ide-helper:models --nowrite --write-mixin --write-eloquent-helper

# Ejecutar Rector (análisis sin cambios)
vendor/bin/rector process --dry-run

# Ejecutar Rector (aplicar cambios)
vendor/bin/rector process
```

### ⚠️ `php artisan test` no existe en este proyecto

El comando `php artisan test` no está disponible. Usar siempre `php vendor/bin/paratest`
o `php vendor/bin/phpunit`.

---

## Convenciones de código

### Idioma

- Los **nombres de modelos, rutas, vistas y variables de negocio** están en **español** (ej. `Actividad`, `Unidad`, `Tarea`, `alumno`).
- La infraestructura técnica (traits, helpers genéricos, comentarios de código) puede estar en inglés.
- Los mensajes de usuario y traducciones residen en `lang/`.

### Modelos Eloquent

- Usar `SoftDeletes` en todos los modelos con datos de dominio relevantes.
- Usar `HasCachedQueries` (y `ModelRelationships` cuando corresponda) de `ymigval/laravel-model-cache` para cachear consultas.
- Registrar actividad con `LogsActivity` de `spatie/laravel-activitylog` implementando `getActivitylogOptions()`.
- Añadir `@mixin IdeHelperNombreModelo` en el docblock de clase para soporte IDE.
- Los modelos clonables deben declarar `$cloneable_relations` y `$clone_exempt_attributes`.

### Controladores

- Todos los controladores requieren `auth` middleware en el constructor.
- Los controladores siguen el patrón resource (CRUD): `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`.
- La autorización se gestiona con Gates/Policies. Usar `$this->authorize()` o checks de rol explícitos.

### Formularios y validación

- Usar Form Requests (`app/Http/Requests/`) para validación compleja.
- Los campos requeridos se documentan en los tests (`$required` array).

### Livewire

- Los componentes Livewire residen en `app/Livewire/`.
- Usar para interactividad en formularios y modales sin recargar página.

---

## Convenciones de tests

### Estructura de directorios

```
tests/
├── Feature/
│   ├── Alumno/          # Tests desde el punto de vista del alumno
│   ├── Coverage/        # Tests auxiliares para cobertura de métodos difíciles de alcanzar
│   ├── Estructura/      # CRUD de entidades estructurales (Curso, Unidad, Actividad...)
│   ├── Evaluacion/      # CRUD de evaluación (Rubric, Skill, Milestone...)
│   ├── Profesor/        # Tests desde el punto de vista del profesor
│   ├── Recursos/        # CRUD de recursos de actividades
│   ├── SafeExam/        # Tests de Safe Exam Browser
│   └── Usuarios/        # CRUD de usuarios, grupos, equipos, roles
├── Unit/                # Tests unitarios de modelos y helpers
└── Browser/             # Tests Dusk (browser end-to-end)
    ├── Actividades/     # Flujos de actividades (T1_TareasTest, ...)
    ├── Concerns/        # Traits compartidos (BrowserUiHelpers)
    ├── Rubrics/         # Flujos de rúbricas
    ├── Sitio/           # Tests de sitio (login, CRUD admin, roles...)
    └── screenshots/     # Capturas automáticas en fallos Dusk
```

### Patrones de test

- Todos los tests de Feature usan `DatabaseTransactions` (no `RefreshDatabase`).
- La clase base `Tests\TestCase` provee el helper `crearUsuarios()` que crea usuarios con todos los roles.
- Los tests de autorización siguen el patrón:
  ```php
  public function testIndex()          // usuario con permiso → 200
  public function testNotXxxNotIndex() // usuario sin permiso → 403
  public function testNotAuthNotIndex()// no autenticado     → redirect login
  ```
- El nombre de los tests sigue `camelCase` con prefijo `test`.
- `setUp()` siempre llama a `parent::setUp()` y `parent::crearUsuarios()`.
- PHPUnit está configurado con `stopOnFailure="true"`: corregir el primer fallo antes de continuar.
- **PHPUnit 12**: usar `createStub()` en lugar de `createMock()` cuando no se necesitan expectativas. `createMock()` sin configurar expectativas genera un PHPUnit Notice.

### Tests Dusk (browser)

- Ejecutar con `make dusk` desde `despliegue/dev/` (hace `migrate:fresh --seed` antes).
- Los tests Dusk usan la DB con datos del seeder (IDs secuenciales). No usar IDs hardcodeados; buscar por campos únicos:
  ```php
  User::where('email', 'alumno@ikasgela.test')->value('id')
  Actividad::where('nombre', 'like', '%nombre%')->first()
  Tarea::where('estado', 30)->latest()->first()
  ```
- El trait `Tests\Browser\Concerns\BrowserUiHelpers` provee `loginAs()`, `logoutToPortada()` y `assertNoAppErrors()`. Todos los tests Dusk deben usar `use BrowserUiHelpers`.
- Las capturas de pantalla en fallos se guardan en `tests/Browser/screenshots/`.

### Fixtures y factories

- Usar `Model::factory()->create()` para crear datos de prueba.
- Los factories residen en `database/factories/`.
- Para tests que necesiten contexto de curso, usar `session(['filtrar_curso_actual' => $id])`.

---

## Conocimiento técnico acumulado

### Trampas conocidas (NO hacer)

- **`$actividad->tarea` es un alias de pivot, NO una relación**: `User::actividades()` usa `.as('tarea')->withPivot([...])`. Añadirlo a `->with()` lanza `Call to a member function addEagerConstraints() on array`.
- **`Etiquetas::etiquetas()` devuelve un array PHP, no una relación Eloquent**: `array_map('trim', explode(',', $this->tags))`. Añadirlo a `->with()` lanza `addEagerConstraints() on array`.
- **`load()` vs `loadMissing()`**: usar `loadMissing()` cuando la relación puede estar ya cargada (evita re-query). Usar `load()` solo cuando se necesita forzar la recarga.
- **`email:rfc,dns` falla en tests**: los dominios ficticios (ej. `test.com`) no resuelven MX desde el contenedor. Usar siempre el helper `email_rule()` (`app/Helpers/ValidacionEmail.php`) que devuelve `email:rfc` en testing y `email:rfc,dns` en producción.
- **`->pluck()` en paginadores**: si la query devuelve `LengthAwarePaginator`, usar `->getCollection()->pluck('id')->toArray()` en vez de `->pluck('id')->toArray()`.

### Paginación con persistencia (`PaginarUltima`)

El trait `app/Traits/PaginarUltima.php` recuerda la página entre recargas:
```php
paginate_ultima($coleccion, int $items_per_page = -1, string $key = 'pagina', ?string $session_scope = null)
```
- **Clave de sesión**: `'paginador_{scope}_{key}'` donde `scope` es `request()->route()->getName()` por defecto.
- El parámetro `$session_scope` permite compartir clave entre rutas distintas (ej. `profesor.index` y `profesor.tareas` comparten `'profesor.disponibles'`).

### Selección múltiple y acciones en bloque (TareaController)

- `borrarMultiple(User $user, Request $request)` → redirige a `profesor.tareas`
- `borrarMultipleActivas(Request $request)` → redirige a `profesor.index`
- `fechaFinalizacionMultipleActivas(Request $request)` → redirige a `profesor.index`

### Helper `email_rule()`

`app/Helpers/ValidacionEmail.php` — cargado automáticamente por `browner12/helpers`:
```php
email_rule() // → 'email:rfc' en testing, 'email:rfc,dns' en producción
```
Usar en todas las validaciones de email en controladores.

### N+1 queries — lecciones aprendidas

- Añadir `->with(['unidad.curso'])` al eager-load del filtro ACT en `ProfesorController::index()`.
- En `tareas()` paginator de `ProfesorController`: `$actividades->with(['unidad.curso'])` antes de paginar.
- En `asignarTareasGrupo/Equipo`: usar `whereIn` batch + `keyBy` en lugar de N llamadas a `User::find()`/`Team::findOrFail()`.
- En `TareaController::borrarTarea()`: `$tarea->loadMissing([...])` al inicio para evitar 3 queries separadas.
- En `ActividadController::edit()`: cachear `$curso = $actividad->load('unidad.curso')->unidad->curso` en variable local.

### Informe del tutor — nota manual

- `aplicarNotaManual()` en `User`: si hay milestone activo y NO tiene nota manual propia, mostrar el **cálculo** del milestone (no caer en la nota manual del curso).
- La nota manual del curso solo se aplica si no hay ningún milestone seleccionado.

---

## Variables de entorno relevantes

| Variable | Descripción |
|---|---|
| `GITEA_ENABLED` | Activar integración con Gitea |
| `GITLAB_ENABLED` | Activar integración con GitLab |
| `REPO_CACHE_DAYS` | Días de caché de repositorios |
| `ELOQUENT_CACHE_TIME` | Segundos de caché de consultas Eloquent |
| `PDF_REPORT_ENABLED` | Habilitar generación de informes PDF |
| `EXCEL_REPORT_ENABLED` | Habilitar exportación Excel |
| `TINYMCE_APIKEY` | API key de TinyMCE (editor de texto rico) |
| `AVATAR_ENABLED` | Habilitar avatares de usuario |
| `PAGINATION_SHORT/MEDIUM/LONG` | Tamaños de paginación (10/25/100) |

## Servidores MCP

La aplicación expone herramientas MCP (Model Context Protocol) a través de `LmsServer` en
`app/Mcp/Servers/LmsServer.php`. Todas las herramientas usan `Response::json()` (nunca
`Response::structured()`, que no funciona correctamente).

### Recursos de IntelliJ — formato de repositorio

Los recursos `IntellijProject` usan el campo `repositorio` para almacenar la referencia al
repositorio Git. **El formato debe ser `usuario/repositorio`** (ejemplo: `ikasgela/mi-proyecto`),
no la URL completa del repositorio.

El `host` por defecto es `gitea`. Si se necesita otro host, pasarlo explícitamente en el
campo `host` (ejemplo: `'gitlab'`).

### Recursos de MarkdownText — rama por defecto

Los recursos `MarkdownText` almacenan la referencia a un archivo en un repositorio Git
mediante los campos `repositorio` y `archivo`. El campo `rama` especifica la rama donde
se encuentra el archivo.

**La rama por defecto es `master`.** Si el archivo se encuentra en otra rama del
repositorio, hay que especificarla explícitamente con `rama`.

Al crear un MarkdownText, los campos `repositorio` y `archivo` son obligatorios.
Al actualizar, todos los campos son opcionales pero al actualizarse `rama` sin valor
explícito se asume `'master'`.

### Convenciones de herramientas MCP

- **Imports de anotaciones**: `Laravel\Mcp\Server\Tools\Annotations\IsReadOnly` (no
  `Laravel\Mcp\Server\Attributes\...`).
- **Respuestas**: usar siempre `Response::json()`.
- **Herramientas de solo lectura**: anotar con `#[IsReadOnly]`.
- **Herramientas destructivas**: anotar con `#[IsDestructive]`.

### Creación de actividades (plantilla por defecto)

Al crear una actividad con la herramienta MCP `CreateActividad`, se crea como plantilla
(`plantilla=true`) por defecto. Si la actividad va a asignarse directamente a estudiantes
y no es una plantilla, hay que pasar explícitamente `plantilla=false`.

---

## Guías para agentes

### Al añadir un nuevo recurso de actividad

1. Crear el modelo en `app/Models/` con `SoftDeletes`, `HasCachedQueries`, `LogsActivity` y `Cloneable`.
2. Añadirlo a `$cloneable_relations` en `Actividad`.
3. Crear el controlador en `app/Http/Controllers/` con middleware `auth`.
4. Registrar las rutas en `routes/web.php`.
5. Crear las vistas en `resources/views/`.
6. Añadir tests CRUD en `tests/Feature/Recursos/` con los tres métodos estándar de autorización.

### Al crear un test

- Extender `Tests\TestCase`, usar `DatabaseTransactions`.
- Llamar a `crearUsuarios()` en `setUp()`.
- Seguir el patrón `// Auth → // Given → // When → // Then` en cada test.
- Cubrir al menos: acceso autorizado, acceso denegado por rol, acceso sin autenticar.
- **No usar `createMock()` sin expectativas** — usar `createStub()` (PHPUnit 12).
- **No usar `email:rfc,dns`** directamente en validaciones — usar el helper `email_rule()`.
- Para tests de índices con scope `plantilla: true`: pasar también `session(['filtrar_curso_actual' => $id])`.
- Para tests con procesos externos (git, etc.): usar `Process::fake(['*' => Process::result(output:'ok', exitCode:0)])`.
- Para tests con ficheros temporales: usar `Storage::fake('temp')` y crear ficheros manualmente.

### Al modificar modelos

- Regenerar los helpers de IDE: `php artisan ide-helper:models --nowrite --write-mixin --write-eloquent-helper`
- Crear la migración correspondiente: `php artisan make:migration`.
- Actualizar el factory si existe.

### Al refactorizar

- Ejecutar Rector con `--dry-run` antes de aplicar cambios.
- El código debe ser compatible con PHP 8.4 y Laravel 12.
- No usar `MigrateToSimplifiedAttributeRector` (está excluido en `rector.php`).

### Commits

- Crear un commit al terminar cada característica o corrección relevante.
- Usar mensajes de commit en **español**, en imperativo, formato Conventional Commits.
- Incluir siempre un trailer `Co-authored-by` con la información del agente que realiza el commit. Ejemplo:

  ```
  git commit -m "tipo(scope): descripción del cambio

  Co-authored-by: TuAgente <tu@email.com>"
  ```

- El repositorio de la app es el directorio `ikasgela/` — **no hacer commits en el repositorio raíz**.
- La rama principal de desarrollo es `develop`.
