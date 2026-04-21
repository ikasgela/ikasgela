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

```bash
# Instalar dependencias PHP
composer install

# Instalar dependencias JS y compilar assets
npm install && npm run build

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Limpiar caché de aplicación
php artisan cache:clear && php artisan config:clear && php artisan view:clear

# Ejecutar todos los tests
php artisan test

# Ejecutar tests en paralelo
php artisan test --parallel

# Ejecutar una suite concreta
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Ejecutar tests de un directorio concreto
php artisan test tests/Feature/Estructura/

# Actualizar helpers de IDE (tras cambios en modelos)
php artisan ide-helper:models --nowrite --write-mixin --write-eloquent-helper

# Ejecutar Rector (análisis sin cambios)
vendor/bin/rector process --dry-run

# Ejecutar Rector (aplicar cambios)
vendor/bin/rector process
```

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
│   ├── Estructura/      # CRUD de entidades estructurales (Curso, Unidad, Actividad...)
│   ├── Evaluacion/      # CRUD de evaluación (Rubric, Skill, Milestone...)
│   ├── Profesor/        # Tests desde el punto de vista del profesor
│   ├── Recursos/        # CRUD de recursos de actividades
│   ├── SafeExam/        # Tests de Safe Exam Browser
│   └── Usuarios/        # CRUD de usuarios, grupos, equipos, roles
└── Unit/                # Tests unitarios de modelos y helpers
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

### Fixtures y factories

- Usar `Model::factory()->create()` para crear datos de prueba.
- Los factories residen en `database/factories/`.
- Para tests que necesiten contexto de curso, usar `session(['filtrar_curso_actual' => $id])`.

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
- Usar mensajes de commit descriptivos en español o inglés, en imperativo y en una sola línea.
- Incluir siempre el trailer `Co-authored-by`:

  ```
  git commit -m "Descripción del cambio

  Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>"
  ```
