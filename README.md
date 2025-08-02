# 🔄 N8N Workflow Manager

Une application web moderne pour documenter, partager et gérer vos workflows N8N au sein de votre équipe.

![Laravel](https://img.shields.io/badge/Laravel-12+-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-3-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)
![Inertia.js](https://img.shields.io/badge/Inertia.js-1.0-9553E9?style=for-the-badge&logo=inertia&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

## 💻 Ecrans
**Page d'accueil**
<img width="1431" height="868" alt="Capture d’écran 2025-08-02 à 13 46 56" src="https://github.com/user-attachments/assets/8a31806d-927e-4d5b-8569-3c36c70dbade" />

**Vue d'un Workflow**
<img width="1432" height="867" alt="Capture d’écran 2025-08-02 à 13 47 12" src="https://github.com/user-attachments/assets/4ddea13d-02b1-4f8b-a29e-bcd8279aa72c" />

<img width="1430" height="867" alt="Capture d’écran 2025-08-02 à 13 47 34" src="https://github.com/user-attachments/assets/7b07de3c-f728-4e3d-a259-02bf4286453d" />

<img width="1428" height="866" alt="Capture d’écran 2025-08-02 à 13 48 05" src="https://github.com/user-attachments/assets/acded215-9212-47fb-907c-5c98d4636b43" />

**Profile Pannel**
<img width="1428" height="868" alt="Capture d’écran 2025-08-02 à 13 48 31" src="https://github.com/user-attachments/assets/1cba9d30-50d5-4649-9fe4-7dd804a324a2" />

**Création d'une documentation de Workflow (génération de description possible par l'IA)
<img width="1426" height="867" alt="Capture d’écran 2025-08-02 à 13 48 49" src="https://github.com/user-attachments/assets/eaf47379-bb6e-4ea1-9ac5-271721918422" />

<img width="1428" height="865" alt="Capture d’écran 2025-08-02 à 13 49 06" src="https://github.com/user-attachments/assets/ed6f1303-a677-4684-afdc-1303c0d73934" />

## 🎯 Fonctionnalités

### ✨ Gestion des Workflows
- **CRUD complet** : Créer, lire, modifier, supprimer vos documentations de workflows N8N
- **Upload JSON** : Import direct de vos fichiers workflow N8N (.json)
- **Saisie manuelle** : Éditeur JSON intégré avec validation
- **Formatage automatique** : JSON formatter et coloration syntaxique

### 📝 Documentation
- **Support Markdown** : Descriptions riches avec aperçu en temps réel
- **Rendu HTML** : Conversion automatique Markdown → HTML
- **Métadonnées** : Titre, auteur, dates de création/modification
- **Tags système** : Catégorisation et filtrage avancé
- **Preview N8N** : https://n8n-io.github.io/n8n-demo-webcomponent/

### 🔒 Gestion des Permissions
- **Visibilité granulaire** : Privé, Équipe, Public
- **Contrôle d'accès** : Seuls les propriétaires peuvent modifier/supprimer

### 🎨 Interface Utilisateur
- **Responsive** : Optimisé mobile/tablette/desktop
- **Cards interactives** : Vue grille avec métadonnées
- **Recherche avancée** : Filtres par tags, auteur, titre
- **Tri dynamique** : Par date, titre, dernière modification

### 🔍 Visualisation
- **JSON Viewer** : Affichage formaté avec coloration syntaxique
- **Résumé automatique** : Comptage nœuds, connexions, types
- **Aperçu Markdown** : Rendu temps réel
- **Export** : Téléchargement JSON, copie presse-papier

## 🏗️ Architecture

### Backend - Clean Architecture
```
app/
├── Models/
│   └── Workflow.php              # Modèle Eloquent
├── Repositories/
│   ├── Contracts/
│   │   └── WorkflowRepositoryInterface.php
│   └── WorkflowRepository.php    # Implémentation Eloquent
├── UseCases/
│   └── Workflow/
│       ├── Create/
│       │   ├── CreateWorkflowInput.php
│       │   ├── CreateWorkflowOutput.php
│       │   └── CreateWorkflowAction.php
│       ├── Update/
│       │   ├── UpdateWorkflowInput.php
│       │   ├── UpdateWorkflowOutput.php
│       │   └── UpdateWorkflowAction.php
│       ├── GetList/
│       │   ├── GetWorkflowListInput.php
│       │   ├── GetWorkflowListOutput.php
│       │   └── GetWorkflowListAction.php
│       └── Delete/
│           └── DeleteWorkflowAction.php
├── Presenters/
│   └── Workflow/
│       ├── WorkflowViewModel.php
│       ├── WorkflowListViewModel.php
│       └── WorkflowPresenter.php
├── Http/
│   ├── Controllers/
│   │   └── WorkflowController.php
│   └── Requests/
│       ├── StoreWorkflowRequest.php
│       └── UpdateWorkflowRequest.php
└── Providers/
    └── AppServiceProvider.php    # Bindings DI
```

### Frontend - Vue 3 + Composition API
```
resources/js/
├── Pages/
│   └── Workflows/
│       ├── Index.vue             # Liste des workflows
│       ├── Create.vue            # Formulaire création
│       ├── Show.vue              # Affichage détaillé
│       └── Edit.vue              # Formulaire édition
└── Components/
    └── Workflows/
        ├── WorkflowCard.vue      # Composant carte
        ├── TagBadge.vue          # Badge tag
        ├── VisibilityBadge.vue   # Badge visibilité
        └── JsonViewer.vue        # Visualiseur JSON
```

## 🚀 Installation

### Prérequis
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL/PostgreSQL
- Laravel 12+

### Configuration

1. **Cloner et installer les dépendances**
```bash
git clone <repository-url>
cd n8n-workflows-docs-v2
composer install
npm install
```

2. **Configuration environnement**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Base de données**
```bash
# Configurer .env avec vos paramètres DB
php artisan migrate
```

4. **Assets**
```bash
npm run build
# ou pour le développement
npm run dev
```

5. **Serveur de développement**
```bash
php artisan serve
```

## 📊 Base de Données

### Table `workflows`
```sql
CREATE TABLE workflows (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    workflow_json JSON NOT NULL,
    tags JSON NULL,
    visibility ENUM('private', 'team', 'public') DEFAULT 'private',
    user_id BIGINT NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_visibility_created (visibility, created_at),
    INDEX idx_user_created (user_id, created_at)
);
```

## 🎯 Utilisation

### Créer un Workflow

1. **Via Interface Web**
   - Cliquer sur "Nouveau Workflow"
   - Remplir le formulaire (titre, description Markdown)
   - Upload fichier JSON N8N ou saisie manuelle
   - Ajouter des tags pour la catégorisation
   - Choisir la visibilité (Privé/Équipe/Public)

2. **Validation JSON**
   - Structure N8N obligatoire : `{"nodes": [...], "connections": {...}}`
   - Validation automatique en temps réel
   - Formatage JSON automatique

### Recherche et Filtrage

```php
// Exemples de filtres supportés
$filters = [
    'search' => 'API synchronisation',  // Titre ou description
    'tags' => ['api', 'webhook'],       // Tags multiples
    'author' => 'John Doe'              // Nom d'auteur
];

// Tri disponible
$sortOptions = [
    'created_at-desc',    // Plus récents (défaut)
    'created_at-asc',     // Plus anciens
    'title-asc',          // Titre A-Z
    'title-desc',         // Titre Z-A
    'updated_at-desc'     // Dernière modification
];
```

### Gestion des Permissions

```php
// Niveaux de visibilité
'private' => 'Visible uniquement par le créateur'
'team'    => 'Visible par l\'équipe (futur)'
'public'  => 'Visible par tous les utilisateurs'

// Permissions automatiques
$workflow->canEdit   = $user->id === $workflow->user_id;
$workflow->canDelete = $user->id === $workflow->user_id;
```

## 🧪 Tests

### Tests Unitaires
```bash
# Lancer tous les tests
php artisan test

# Tests spécifiques
php artisan test --filter WorkflowTest
php artisan test tests/Feature/WorkflowControllerTest.php
```

### Structure de Test
```php
tests/
├── Feature/
│   ├── WorkflowControllerTest.php
│   ├── WorkflowAuthorizationTest.php
│   └── WorkflowValidationTest.php
├── Unit/
│   ├── Models/WorkflowTest.php
│   ├── Repositories/WorkflowRepositoryTest.php
│   └── UseCases/CreateWorkflowActionTest.php
└── TestCase.php
```

## 🔧 API Routes

```php
Route::middleware('auth')->group(function () {
    Route::get('/workflows', [WorkflowController::class, 'index'])
         ->name('workflows.index');
    
    Route::get('/workflows/create', [WorkflowController::class, 'create'])
         ->name('workflows.create');
    
    Route::post('/workflows', [WorkflowController::class, 'store'])
         ->name('workflows.store');
    
    Route::get('/workflows/{slug}', [WorkflowController::class, 'show'])
         ->name('workflows.show');
    
    Route::get('/workflows/{slug}/edit', [WorkflowController::class, 'edit'])
         ->name('workflows.edit');
    
    Route::put('/workflows/{slug}', [WorkflowController::class, 'update'])
         ->name('workflows.update');
    
    Route::delete('/workflows/{slug}', [WorkflowController::class, 'destroy'])
         ->name('workflows.destroy');
});
```

## 📱 Interface Utilisateur

### Pages Principales

**Dashboard (`/workflows`)**
- Vue grille responsive des workflows
- Filtres avancés (recherche, tags, auteur)
- Tri dynamique
- Pagination
- Actions : Voir, Éditer, Supprimer (selon permissions)

**Création (`/workflows/create`)**
- Formulaire complet avec validation
- Upload JSON ou saisie manuelle
- Aperçu Markdown temps réel
- Tags suggérés basés sur l'existant
- Contrôle visibilité

**Affichage (`/workflows/{slug}`)**
- Onglets : Description, JSON, Aperçu visuel
- Métadonnées complètes
- Actions d'export (copie, téléchargement)
- Résumé automatique (nœuds, connexions)

**Édition (`/workflows/{slug}/edit`)**
- Pré-remplissage des données existantes
- Validation JSON temps réel
- Compteurs dynamiques (nœuds/connexions)

### Composants Réutilisables

**WorkflowCard.vue**
- Affichage compact avec métadonnées
- Actions contextuelles
- États visuels (visibilité, tags)
- Modal de confirmation suppression

**JsonViewer.vue**
- Coloration syntaxique
- Modes collapsed/expanded
- Format compact/indenté
- Copie/téléchargement

**TagBadge.vue**
- Couleurs automatiques par catégorie
- Variantes prédéfinies
- Actions de suppression

## 🎨 Design System

### Couleurs Tailwind
```css
/* Palette principale */
--indigo-50: #eef2ff;
--indigo-600: #4f46e5;
--indigo-700: #4338ca;

/* États */
--green-100: #dcfce7;   /* Succès */
--red-100: #fee2e2;     /* Erreur */
--yellow-100: #fef3c7;  /* Warning */
--gray-100: #f3f4f6;    /* Neutre */
```

### Composants UI
- **Cards** : `shadow-sm hover:shadow-md transition-shadow`
- **Boutons** : États hover/focus avec ring-2
- **Formulaires** : Validation visuelle inline
- **Modales** : Backdrop blur avec animations

## 🔄 Workflow JSON Structure

### Format N8N Standard
```json
{
  "nodes": [
    {
      "id": "unique-node-id",
      "name": "HTTP Request",
      "type": "n8n-nodes-base.httpRequest",
      "position": [250, 300],
      "parameters": {
        "url": "https://api.example.com/data",
        "method": "GET"
      }
    }
  ],
  "connections": {
    "HTTP Request": {
      "main": [
        [
          {
            "node": "Set",
            "type": "main",
            "index": 0
          }
        ]
      ]
    }
  }
}
```

### Exemples de Workflows
```json
// Exemple : Notification Slack automatique
{
  "nodes": [
    {
      "id": "webhook-trigger",
      "name": "Webhook",
      "type": "n8n-nodes-base.webhook",
      "parameters": {
        "path": "order-received"
      }
    },
    {
      "id": "slack-notification",
      "name": "Slack",
      "type": "n8n-nodes-base.slack",
      "parameters": {
        "channel": "#orders",
        "text": "Nouvelle commande reçue: {{$json.orderId}}"
      }
    }
  ],
  "connections": {
    "Webhook": {
      "main": [["Slack"]]
    }
  }
}
```

## 🆘 Support

- **Email** : ant.debout@gmail.com

---

**Développé avec ❤️ pour la communauté N8N**

*Cette application facilite la documentation et le partage de workflows N8N au sein des équipes, en suivant les meilleures pratiques de développement moderne.*
