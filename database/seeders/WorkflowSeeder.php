<?php

namespace Database\Seeders;

use App\Models\Workflow;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $workflows = [
            [
                'title' => 'Synchronisation Automatique GitHub - Notion',
                'description' => "# Synchronisation GitHub vers Notion\n\n".
                    "Ce workflow automatise la synchronisation des issues GitHub vers une base Notion.\n\n".
                    "## Fonctionnalités\n\n".
                    "- Création automatique de pages Notion pour chaque nouvelle issue\n".
                    "- Mise à jour du statut en temps réel\n".
                    "- Synchronisation des labels et assignations\n\n".
                    "### Configuration requise\n\n".
                    "- Token d'accès GitHub avec permissions `repo`\n".
                    "- Clé d'API Notion avec accès à la base cible",
                'tags' => ['github', 'notion', 'automation', 'issues'],
                'visibility' => 'public',
                'workflow_json' => json_encode(['nodes' => [
                    ['type' => 'n8n-nodes-base.github'],
                    ['type' => 'n8n-nodes-base.notion']
                ], 'connections' => ['main' => [[['node' => 'GitHub', 'type' => 'main', 'index' => 0]]]]]),
            ],
            [
                'title' => 'Automatisation Newsletter avec Mailchimp',
                'description' => "# Automatisation Newsletter\n\n".
                    "Workflow pour gérer automatiquement l'envoi de newsletters via Mailchimp.\n\n".
                    "## Processus\n\n".
                    "1. Collecte des articles depuis RSS\n".
                    "2. Filtrage et formatage du contenu\n".
                    "3. Création de campagne Mailchimp\n".
                    "4. Envoi programmé\n\n".
                    "### Personnalisation\n\n".
                    "- Templates personnalisables\n".
                    "- Filtres de contenu configurables\n".
                    "- Programmation flexible",
                'tags' => ['mailchimp', 'newsletter', 'automation', 'rss'],
                'visibility' => 'public',
                'workflow_json' => json_encode(['nodes' => [
                    ['type' => 'n8n-nodes-base.rssFeedRead'],
                    ['type' => 'n8n-nodes-base.mailchimp']
                ], 'connections' => ['main' => [[['node' => 'RSS Feed Read', 'type' => 'main', 'index' => 0]]]]]),
            ],
            [
                'title' => 'Pipeline de Traitement d\'Images',
                'description' => "# Pipeline de Traitement d'Images\n\n".
                    "Automatisation du traitement d'images pour le web.\n\n".
                    "## Étapes\n\n".
                    "- Redimensionnement automatique\n".
                    "- Optimisation et compression\n".
                    "- Conversion de formats\n".
                    "- Upload vers CDN\n\n".
                    "### Spécifications\n\n".
                    "- Supporte JPG, PNG, WebP\n".
                    "- Compression intelligente\n".
                    "- Génération de miniatures",
                'tags' => ['images', 'automation', 'optimization', 'cdn'],
                'visibility' => 'public',
                'workflow_json' => json_encode(['nodes' => [
                    ['type' => 'n8n-nodes-base.ftp'],
                    ['type' => 'n8n-nodes-base.httpRequest']
                ], 'connections' => ['main' => [[['node' => 'FTP', 'type' => 'main', 'index' => 0]]]]]),
            ],
            [
                'title' => 'Monitoring Infrastructure AWS',
                'description' => "# Monitoring AWS\n\n".
                    "Surveillance automatisée de l'infrastructure AWS avec alertes.\n\n".
                    "## Fonctionnalités\n\n".
                    "- Surveillance des métriques CloudWatch\n".
                    "- Alertes Discord/Slack\n".
                    "- Rapports quotidiens\n".
                    "- Détection d'anomalies\n\n".
                    "### Métriques surveillées\n\n".
                    "- Utilisation CPU/RAM\n".
                    "- Coûts estimés\n".
                    "- Santé des services",
                'tags' => ['aws', 'monitoring', 'alerting', 'devops'],
                'visibility' => 'private',
                'workflow_json' => json_encode(['nodes' => [
                    ['type' => 'n8n-nodes-base.aws'],
                    ['type' => 'n8n-nodes-base.discord']
                ], 'connections' => ['main' => [[['node' => 'AWS', 'type' => 'main', 'index' => 0]]]]]),
            ],
            [
                'title' => 'Intégration CRM - Slack',
                'description' => "# Intégration CRM vers Slack\n\n".
                    "Synchronisation bidirectionnelle entre CRM et Slack.\n\n".
                    "## Automatisations\n\n".
                    "- Notifications de nouveaux leads\n".
                    "- Mise à jour des opportunités\n".
                    "- Assignation automatique\n".
                    "- Rapports de performance\n\n".
                    "### Canaux Slack\n\n".
                    "- #nouveaux-leads\n".
                    "- #opportunites\n".
                    "- #rapports-crm",
                'tags' => ['crm', 'slack', 'sales', 'automation'],
                'visibility' => 'private',
                'workflow_json' => json_encode(['nodes' => [
                    ['type' => 'n8n-nodes-base.hubspot'],
                    ['type' => 'n8n-nodes-base.slack']
                ], 'connections' => ['main' => [[['node' => 'Hubspot', 'type' => 'main', 'index' => 0]]]]]),
            ],
        ];

        foreach ($workflows as $workflow) {
            Workflow::create([
                'title' => $workflow['title'],
                'slug' => Str::slug($workflow['title']),
                'description' => $workflow['description'],
                'tags' => $workflow['tags'],
                'visibility' => $workflow['visibility'],
                'workflow_json' => $workflow['workflow_json'],
                'user_id' => $user->id,
            ]);
        }
    }
}
