# AI Agent Instructions for Painel Project

## Project Overview
This is a Zend Framework 2 (ZF2) based application providing a dashboard and control panel system. The application is structured as a modular MVC system with multiple independent modules handling different aspects of the application.

## Key Architecture Components

### Module Structure
- **Application**: Core application module with base controllers and services
- **Dashboard**: Main dashboard functionality and data visualization
- **Classes**: Common utilities and shared services
- **Autenticacao**: Authentication and authorization
- **Mobile**: Mobile-specific views and controllers
- **RiskManager**: Risk management and event handling

### Data Flow
1. Requests route through `public/index.php`
2. Module routing defined in `module/{ModuleName}/config/module.config.php`
3. Controllers extend `Estrutura\Controller\AbstractEstruturaController`
4. Views stored in `module/{ModuleName}/view/`
5. Data cached in `data/cache/` for performance

## Development Workflow

### Project Setup
```bash
composer install
```

### Configuration
- Environment configs in `config/autoload/`
- Module configs in `module/{ModuleName}/config/module.config.php`
- Development settings in `config/autoload/development.php`

### Key Integration Points
1. Risk Manager API
   - Services in `module/RiskManager/`
   - API controllers in `module/Application/src/Application/Controller/ApiController.php`

2. Dashboard Data Processing
   - Data services in `module/Dashboard/src/Dashboard/Service/`
   - JSON cache in `data/json_api/`

3. Mobile Integration
   - Mobile-specific layouts in `module/Mobile/view/layout/mobile.phtml`
   - Controllers in `module/Mobile/src/Mobile/Controller/`

## Project Conventions

### Controller Structure
- Controllers must extend `AbstractEstruturaController`
- Actions return `ViewModel` or `JsonModel` objects
- API responses use `ApiView` service for consistent formatting

### Service Layer
- Services extend `AbstractEstruturaService`
- Data access through service classes in `{Module}/Service/`
- Session management via `Zend\Session\Container`

### View Templates
- Layout files in `module/{ModuleName}/view/layout/`
- Template inheritance through `layout/layout.phtml`
- Partial views in respective module view directories

### Data Storage
- Cache files in `data/cache/`
- JSON storage in `data/json/` and `data/json_api/`
- Settings files in `data/settings/`

## Common Patterns
1. API Response Format:
```php
return $this->apiView->successReturn($data, 'Success message');
// or
return $this->apiView->errorReturn('Error message');
```

2. Session Access:
```php
$container = new \Zend\Session\Container('UsuarioApi');
```

3. Form Handling:
```php
$form = new \Classes\Form\Triagem();
if ($request->isPost()) {
    $form->setData($request->getPost()->toArray());
}
```

## File Locations Reference
- Controllers: `module/{ModuleName}/src/{ModuleName}/Controller/`
- Views: `module/{ModuleName}/view/`
- Config: `module/{ModuleName}/config/module.config.php`
- Services: `module/{ModuleName}/src/{ModuleName}/Service/`