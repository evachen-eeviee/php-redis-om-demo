# Symfony Redis OM Demo

Projet de démonstration pour la librairie [php-redis-om](https://github.com/clementtalleu/php-redis-om), un Object Mapper Redis pour PHP.

## Stack technique

- **PHP 8.4** (FrankenPHP)
- **Symfony 7.4**
- **Redis Stack** (Redis + RediSearch + RedisJSON)
- **php-redis-om** (talleu/php-redis-om)

## Installation

```bash
docker compose up -d --build
```

- App : https://localhost (certificat auto-signé)
- RedisInsight : http://localhost:8001

## TODO

### Setup initial

- [X] Installer `talleu/php-redis-om` via Composer
- [X] Installer Twig (`symfony/twig-bundle`)
- [X] Installer le formulaire Symfony (`symfony/form`, `symfony/validator`)
- [X] Enregistrer le bundle dans `config/bundles.php` : `Talleu\RedisOm\Bundle\TalleuRedisOmBundle::class => ['all' => true]`
- [X] Configurer la connexion Redis (env `REDIS_URL`)

### Entités Redis

- [X] Créer une entité `Book` (id, title, author (qui est un User), enabled, category, description, publishedAt, price)
- [X] Créer une entité `Category` (id, title)
- [X] Créer une entité `User` (id, name, email, age, createdAt)
- [X] Créer une entité `Comment` (id, author, book, content, createdAt)
- [X] Vérifier le mapping avec les attributs `#[RedisOm\Entity]`, `#[RedisOm\Id]`, `#[RedisOm\Property]`
- [X] Indexer les champs pertinents pour la recherche (`index: true`)
- [X] Lancer la migration : `bin/console redis-om:migrate`

### Formulaires & Controllers

- [X] Créer un `BookController` avec CRUD complet (list, create, show, edit, delete)
- [X] Créer un `UserController` avec CRUD complet
- [X] Créer un `CategoryController` avec CRUD complet
- [X] Créer les `FormType` associés (BookType, UserType, CategoryType)
- [X] Toute la partie CRUD préfixée par /admin
- [ ] Créer une page "vue des ouvrages" qui affiche les livres activés
- [ ] Créer des filters
- [ ] Faire la page "détail d'un ouvrage"
- [ ] Afficher les commentaires
- [ ] Permettre de poster un nouveau commentaire
- [ ] Gérer la validation des formulaires

### Templates & UI

- [X] Créer un layout de base (`base.html.twig`) avec navigation
- [ ] Templates de listing pour chaque entité
- [ ] Templates de formulaire (create/edit)
- [ ] Template de détail (show)
- [ ] Messages flash pour les actions (create, update, delete)

### Fonctionnalités de recherche

- [ ] Implémenter `findAll()` pour chaque entité
- [ ] Implémenter `findBy()` avec critères de recherche
- [ ] Implémenter `findOneBy()` 
- [ ] Ajouter un formulaire de recherche/filtre sur les listings
- [ ] Tester le tri (`orderBy`) sur les collections

### Fonctionnalités avancées

- [ ] Tester le support RedisJSON (stocker des objets imbriqués)
- [ ] Tester l'auto-expiration (TTL sur les entités)
- [ ] Tester les types avancés (DateTimeImmutable, arrays, nested objects)
- [ ] Créer une page dashboard avec des stats (nombre d'objets par entité)

### Tests & Validation

- [ ] Vérifier que les objets sont bien persistés dans Redis
- [ ] Vérifier la recherche par critères
- [ ] Vérifier le tri et la pagination
- [ ] Vérifier la suppression
- [ ] Vérifier via RedisInsight que les données sont correctes

### Préparation V1

- [ ] Documenter les fonctionnalités testées et leur statut
- [ ] Identifier les éventuels bugs ou limitations
- [ ] Mettre à jour `php-redis-om` vers la V1 quand disponible
- [ ] Relancer les tests pour vérifier la rétrocompatibilité
- [ ] Documenter les breaking changes éventuels
