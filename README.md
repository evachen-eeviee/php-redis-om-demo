# Symfony Redis OM Demo

Projet de démonstration pour la librairie [php-redis-om](https://github.com/clementtalleu/php-redis-om), un Object Mapper Redis pour PHP.

## ✨ Demo Features

- **Full CRUD** : Management of Books, Users, and Categories
- **Advanced Search** : Filters and sort data
- **Nesting & Relations** : Handing complew object and relations via RedisJSON
- **Admin Interface** : Complete back-office under /adùin using Symfony Forms
- **Developer Experience** : Seamless integratrion with the RedisObjectManager

## 🛠️ Stack technique

- **PHP 8.4** (FrankenPHP)
- **Symfony 7.4**
- **Redis Stack** (Redis + RediSearch + RedisJSON)
- **php-redis-om** (talleu/php-redis-om)

## 📝 Installation & Setup

### 1. Clone the repository

#### Fork the repository 🍴

To use the demo, you first have to fork the repository to clone it.
![](/public/fork.png)

Once you created the fork, depending on preferences, you can clone it by using HTTPS or SSH methods by clicking first on the green button named code

![](/public/clone.png)

You have to copy it, then in your terminal, go to the directory where you want the demo to be and clone it

```bash
git clone [paste your https or ssh here]
```

### 2. Spin up the infrastructure

The demo uses docker to streamline the PHP and Redis Stack installation, so you need to install it beforehand

```bash
docker compose up -d --build
```

### 2. Access the services

- Web-App : https://localhost (certificat auto-signé)
- RedisInsight : http://localhost:8001

To access the demo : 

- Demo : https://localhost/books

### 3. Library Initialization

If you are starting fresh or updating the library : 

```bash
#If you are outside of the container

# Install the development version
docker compose exec php composer require talleu/php-redis-om:dev-main

# Generate Redis indexes (Migration)
docker compose exec php bin/console redis-om:migrate

#----------------------------------------------------#

# If you are inside of the container

# Install the development version
composer require talleu/php-redis-om:dev-main

# Generate Redis indexes (Migration)
bin/console redis-om:migrate
```
Depending on your configuration, use phpredis or Predis

## 🎯 How to Use the Demo 

### Entity Mapping
Entities are located in src/Entity

```php
//------------------------------------------Exemple-------------------------------------//
#[RedisOm\Entity] // To declare an entity
class Book {
    #[RedisOm\Id] // Id is automatically created
    #[RedisOm\Property]
    public int $id;

    #[RedisOm\Property(index: true)] // Enables searching/filtering on this field
    public string $title;

    #[RedisOm\Property(index:true)]
    public \DateTimeImmutable $publishedAt;
}
```
### Using the Manager

In your controllers (src/Controller), the RedisObjectManagerInterface is injected to handle data via forms

```php
//------------------------------------------Exemple-------------------------------------//
public function create(RedisObjectManagerInterface $manager) {
    $book = new Book();
    $form = $this->createForm(BookType::class, $book);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        // Prepare the object for Redis
        $manager->persist($book);
        
        // Execute the commands (HMSET, JSON.SET, etc. depending on your mapping)
        $manager->flush();

        $this->addFlash('success', 'Book saved to Redis!');
        return $this->redirectToRoute('app_book_index');
    }

    return $this->render('admin/book/new.html.twig', [
        'form' => $form,
    ]);
}
```
### Form Type

The library supports standard Symfony Form Types. Because the entities use standard PHP properties, no special configuration is required for the FormType.  
They are used to create object in Redis.

```php
//------------------------------------------Exemple-------------------------------------//
class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('publishedAt', DateType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
            ])
            ->add('price', NumberType::class);
    }
}
```

### Retrieval and Filtering

Use the Repository to fetch your data : 

```php
//------------------------------------------Exemple-------------------------------------//
$repository = $manager->getRepository(Book::class);

// Get everything
$books = $repository->findAll();

// Search with criteria and sorting
$books = $repository->findBy(
    ['author' => 'Jack London'], // criteria
    ['publishedAt' => 'DESC'], // order by
    10 // Limit
);
```

## 🛣️ Every Route and Their Function

**User** :

*User*
- **https://localhost/user/new** : Create a new user

*Book*
- **https://localhost/books** : List of Book enabled
  - **https://localhost/books/{id}**: Show the book with this id, you can comment on this page


**Admin** :

*Category*  
- **https://localhost/category** : List of all created category, you can delete them or show all the book with this category
  - **https://localhost/category/{id}** : List of books which has this category
  - **https://localhost/category/new** : Create a new category

*Book*
- **https://localhost/admin/books** : List of all the books, enabled or not
  - **https://localhost/admin/books/new** : Create a book
  - **https://localhost/books/edit/{id}** : Edit a book with this specific id, you can delete them if they are disabled

*User*
- **https://localhost/user** : List of all created user, you can delete and edit them
  - **https://localhost/user/edit/{id}** : Edit a user with this specific id
- **https://localhost/justine** : Use of findOneBy on a user withe the name Justine, and usage of findMultiple with user id (if you want to test it you have to change the id)

*Comment*

- **https://localhost/comment** :  List of all created comment, you can delete or edit them
  - **https://localhost/comment/edit/{id}** : Edit a comment with this specific id

*Dashboard*

- **https://localhost/dashboard** : Show how many data of each entity we have

