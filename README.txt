#-------------------   Despues de clonar el proyecto base. ---------------------------------#

1) Cambiar nombre de Base de Datos y Crearla:

    a) Editar archivo .env
        DATABASE_URL=mysql://root:root@docker_db_1:3306/s4jwtbase

2) Crear migraciones
    a) Borrar contenido de carpeta src/Migrations
    b) Ejecutar por consola :
        php bin/console make:migration

3) Ejecutar migraciones
    a) php bin/console doctrine:migrations:migrate

4) Navegar por browser a :
    http://localhost:83/symfony4_jwt_base/public/

   Si salio ok :

    "Welcome to de API Platform Page"
5) Load Users and data example
    a) php bin/console doctrine:fixtures:load

6) Validar si anda JWT ( Solo si se ejecuto el paso 5 )  :
    a) Opcion con POSTMAN ( o similar  )

    Method: POST
    URL: http://localhost:83/symfony4_jwt_base/public/index.php/api/login_check
    BODY  raw (application/json)
    {
        "username": "admin",
        "password": "secret123#"
    }

    b) Opcion con curl
    curl -d '{"username": "admin","password": "secret123#"}' -H "Content-Type: application/json" -X POST http://localhost:83/symfony4_jwt_base/public/index.php/api/login_check

Resultado esperado:
    En ambos casos debe retornar TOKEN
    {"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NzMxMzQ1MzIsImV4cCI6MTU3MzEzODEzMiwicm9sZXMiOlsiUk9MRV9TVVBFUkFETUlOIl0sInVzZXJuYW1lIjoiYWRtaW4ifQ.uqKZFtyDKR4ivXzsMVaopI8f5_mOFJnN2NvtfZVuDyN5L4IkVnRlpm3MM9Phkd9odfBkZJQ3ADe3MpKybK26TRvlk9hYGwXW69MyoVc80SnUvAfQogZDpdYrbgqs-_-1cxFBfnDZM7aBmgxMcGBr91XTLesix8nxz1RpPLqrzqekY0oTKgNJ8QHZzA1qhNEewAaLqO-uObKthysGREQbCOJ84modPV1OaDcJOYeEiMmyuDs1HhCD6Fxb34XVSf0bIJRUsPqv_q-00mH_jas00vZ8UcjOoxvni1K2B3BeXybWwA_rbpoX-uUQHTur-Ix7E3tXUn2MQTKCM5Vfyg9SyYidnHhH7FAMHVvpFc73mZuLL57FkurPHKERLpk29dnecJI8rNr9eNSB_Cxvr2D6GaE8rcNV1txUEM-ci2F-o2p1fC23Cd21ahwy15aRdt56h0zH2jvl8KFoj-WOEagPZZQfalNfWiW3yH5zGarIByAg7oEoR3HMikUf_h9dvRhEmOlKAyFJvs08KMhot1WAsYdu_oHhxwB2A304Gy4wOJsIISoSbAvCMG72JEG6FTJ4JoZjfa2WgRw9StjeBichsJY54EMWfrtZhQS-IPV3f9QYhmBwBJwqSRI0_tJsugjkoUk8WbdaGfNkCm0Dy5JTnG7nysw3OymNMfd0WY9CzxM"}








#----------------------------------- Paso a paso de como se creo el proyecto ---------------------------------------#

#-----------------------Crear proyecto Symfony 4 - EasyAdmin - Api Platform -----------------------#

Se asume que esta instalado:
    php version > 7.3,
    composer

NOTA: Las URL que veran a continuacion se tratan symfony en docker con php 7.3 expuesto en puerto :83

1) composer create-project symfony/skeleton project -vvv
2) composer require annotations
3) composer require symfony/orm-pack
4) composer require symfony/maker-bundle --dev

    Prueba: http://localhost:83/symfony4_jwt_base/public/
            http://localhost:83/symfony4_jwt_base/public/index.php/blog/1
5) Generando entitys:

    php bin/console make:entity

6) Una vez generada la entidad generamos el archivo de migraciones para crear tablas, actualizar campos, etc

    6.1 Generar archivos de migracion
        php bin/console make:migration

    6.2 Ejecuar migraciones
        php bin/console doctrine:migrations:migrate

7) Serializar/Deserializar entidades y
    composer require serializer

8) Doctrine fictures bundle
    composer require --dev doctrine/doctrine-fixtures-bundle

    8.1 Generar fixtures en src/DataFixtures/AppFixtures.php
    8.2 Ejecutar Fixtures
        php bin/console doctrine:fixtures:load


#--------------------------------Install Easy Admin----------------------------------------------#

1) composer require admin
2) Enable entitys for administration in:
    /config/packages/easy_admin.yaml
    Example:
    easy_admin:
        entities:
    #        # List the entity class name you want to manage
            - App\Entity\BlogPost
3) Test plugin:
    http://localhost:83/symfony4_jwt_base/public/index.php/admin/?action=list&entity=BlogPost

#-------------------------------------------------------------------------------------------------#


#--------------------------------Install Api Platform ----------------------------------------------#
1) composer require api -vvv
2) Modificar Entitys colocando @ApiResources de Api Platform a las Entity
    use ApiPlatform\Core\Annotation\ApiResource;
    /**
     * @ORM\Entity(repositoryClass="App\Repository\BlogPostRepository")
     * @ApiResource()
     */
    class BlogPost



#---------------------------------------------------------------------------------------------------#


#-------------------------------- Create User class implements UserInterface -----------------------#

1) php bin/console make:entity
    > User

    Una vez generada la entidad generamos el archivo de migraciones para crear tablas, actualizar campos, etc

      1.1 Generar archivos de migracion
           php bin/console make:migration

      1.2 Ejecuar migraciones
           php bin/console doctrine:migrations:migrate

2) Configuramos Encoders para las claves

    a) En config/packages/security.yaml, agregar:
     encoders:
            App\Entity\User: bcrypt

3) En los DataFixtures-> AppFixtures.php agregar constructor que reciba el passwordEncoder
     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
   Utilizar esta variable para encryptar las claves
#---------------------------------------------------------------------------------------------------#

#-----------------------------------Install Faker para usar en Fixtures ----------------------------#

Esto es para generar contenido dummy de manera muy facil, ver su uso en este proyecto
    DataFixtures->AppFixtures.php

1) composer require --dev fzaninotto/faker
    Solo se necesita en develop por eso el --dev


#----------------------------------- API Platform Disabling Operations ----------------------------#

Sobre la entidad que queremos trabajar se puede ocultar endpoints y properties.
Por ejemplo de la entidad usuario se permiten solo los get de 1 usuario y no los listados.
Ademas se muestran solo las columnas que tienen group "read"

Al definir collectionOperations vacio, indicamos que no exponga endpoints de getAll
Y con itemOperation "get" le decimos que solo get de 1 item (no post, ni put)
@ApiResource(
 *      itemOperations={"get"},
 *      collectionOperations={},
 *      normalizationContext={
            "groups" = { "read" }
 *     }
 * )

#--------------------------------------------PasswordHashSubscriber------------------------------------#

1) Crear carpeta src/EventSubscriber
2) Crear clase PasswordHashSubscriber implements EventSubscriberInterface

3) Ver servicios creados
    php bin/console debug:container PasswordHashSubscriber



#--------------------------------------------JWT--------------------------------------------------------#

1) Instalar libreria para JWT
    composer require lexik/jwt-authentication-bundle

2) Revisar que se haya creado el archivo config/packages/lexik_jwt_authentication.yaml
    lexik_jwt_authentication:
        secret_key: '%env(resolve:JWT_SECRET_KEY)%'
        public_key: '%env(resolve:JWT_PUBLIC_KEY)%'
        pass_phrase: '%env(JWT_PASSPHRASE)%'
3) Revisar que se este levantando el bundle en bundles.php
    Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle::class => ['all' => true],

4) Revisar que se crearon los parametros de environment en .env de la raiz del proyecto
    ###> lexik/jwt-authentication-bundle ###
    JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
    JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
    JWT_PASSPHRASE=mprod
    TOKEN_TTL=3600
    ###< lexik/jwt-authentication-bundle ###

5) Generar nueva carpeta jwt en config/jwt, para generar las claves privadas y publicas
    Generate the SSH keys:
    $ mkdir -p config/jwt
    5.1) Darle permisos de escritura a config/ chmod 777 -R config/*
    5.2) Generar clave publica y privada, la clave que solicita es la que configuramos arriba en JWT_PASSPHRASE
        $ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
        $ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
    5.3) Darle permisos de lectura al privatepm en config/jwt/
        $ chmod 644 private.pem
    5.4) Crear un .htaccess en la carpeta /public con el siguiente contenido.
        Esto se hace para que apache no descarte la cabecera de Authorization
        RewriteEngine On
        RewriteCond %{HTTP:Authorization} ^(.*)
        RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]


6) Configurar User Provider
    read: https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#configuration
    6.1) Editar config/packages/security.yaml y modificar la seguridad in_memory por la base de datos
        Antes:
            providers:
                    in_memory: { memory: ~ }

        Despues:
             providers:
                    # in_memory: { memory: ~ }
                    database:
                        entity:
                            class: App\Entity\User
                            property: username
    6.2) Configurar Firewall en config/packages/security.yaml
        Antes:
            firewalls:
                    dev:
                        pattern: ^/(_(profiler|wdt)|css|images|js)/
                        security: false
                    main:
                        anonymous: true
        Despues:
            firewalls:
                    dev:
                        pattern: ^/(_(profiler|wdt)|css|images|js)/
                        security: false
                    api:
                        pattern: ^/api
                        stateless: true
                        anonymous: true
                        json_login:
                            check_path: /api/login_check
                            success_handler: lexik_jwt_authentication.handler.authentication_success
                            failure_handler: lexik_jwt_authentication.handler.authentication_failure
                        guard:
                            authenticators:
                                - lexik_jwt_authentication.jwt_token_authenticator

    6.3) Configurar access control en config/packages/security.yaml
       access_control:
               - { path: ^/api/docs, roles: IS_AUTHENTICATED_FULLY }
               - { path: ^/api/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
               - { path: ^/api,       roles: IS_AUTHENTICATED_FULLY }

    6.4) Configurar Rutas de login
        Configure your routing into config/routes.yaml :

        api_login_check:
            path: /api/login_check

#----------------------------------- Swift Mailer Bundle ----------------------------#

Instalar libreria para envio de emails

1) composer require symfony/swiftmailer-bundle
2) Este bundle agrega una configuracion en el .env y en /config/packages/swiftmail.yaml
MAILER_URL=gmail://medrasoftnet:PASWORDXXXXXX@localhost
3) Navegar a https://support.google.com/mail/?p=BadCredentials
4) habilitar el acceso desde apps menos seguras https://support.google.com/accounts/answer/6010255
5) Entrar en gmail (correo) y en Configuraciones->Reenvío y correo POP/IMAP
 habilitar el acceso IMAP

Acceso IMAP:
(acceder a Gmail desde otros clientes mediante IMAP)
Más información
Estado: El acceso IMAP está habilitado
	Habilitar acceso IMAP
	Inhabilitar IMAP
6) Deshabilitar el captcha para la cuenta
    https://accounts.google.com/DisplayUnlockCaptcha
   Hacer click en "continuar"

   Exito:
   Se habilitó el acceso a la cuenta.
   Intenta volver a acceder a tu Cuenta de Google desde el nuevo dispositivo o aplicación.

#----------------------------------- Vich Uploader Bundle ----------------------------#
Para upload de imagenes
1) composer require vich/uploader-bundle
2) Configure config/packages/vich_uploader.yaml
vich_uploader:
    db_driver: orm

    mappings:
        images:
            uri_prefix: /images
            upload_destination: '%kernel.project_dir%/public/images'
            namer: Vich\UploaderBundle\Naming\UniqidNamer
3) Add Entity for save image:
Entity/Image.php:
        <?php


        namespace App\Entity;

        use ApiPlatform\Core\Annotation\ApiResource;
        use Doctrine\ORM\Mapping as ORM;
        use Vich\UploaderBundle\Mapping\Annotation as Vich;

        /**
         * @ORM\Entity()
         * @Vich\Uploadable()
         * @ApiResource()
         */
        class Image
        {
            /**
             * @ORM\Id()
             * @ORM\GeneratedValue()
             * @ORM\Column(type="integer")
             */
            private $id;

            /**
             * "images" => es el nombre de la propiedad configurada en vich_uploader.yaml
             * "url" debe ser una propiedad de la misma clase
             * @Vich\UploadableField(mapping="images", fileNameProperty="url")
             */
            private $file;

            /**
             * @ORM\Column(nullable=true)
             */
            private $url;

            /**
             * @return mixed
             */
            public function getId()
            {
                return $this->id;
            }

            /**
             * @param mixed $id
             */
            public function setId($id): void
            {
                $this->id = $id;
            }

            /**
             * @return mixed
             */
            public function getFile()
            {
                return $this->file;
            }

            /**
             * @param mixed $file
             */
            public function setFile($file): void
            {
                $this->file = $file;
            }

            /**
             * @return mixed
             */
            public function getUrl()
            {
                return $this->url;
            }

            /**
             * @param mixed $url
             */
            public function setUrl($url): void
            {
                $this->url = $url;
            }


        }

4) Run migrations for create Image table
    a) php bin/console make:migration
    b) php bin/console doctrine:migrations:migrate

#----------------------------------- API Platform Filters ----------------------------#

Order:
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *              "id",
 *              "published",
 *              "title"
 *     },
 *     arguments={"orderParameterName"="_order"}
 * )

    {{url}}/api/blog_posts?order[published]=desc
    {{url}}/api/blog_posts?_order[published]=desc&_order[title]asc

Filter:

Rango:
    {{url}}/api/blog_posts?id[gt]=652&id[lt]=655 "lt"=> Menor que
    {{url}}/api/blog_posts?id[gt]=652   --- "gt"=> Mayor que

    {{url}}/api/blog_posts?id[gte]=652&id[lte]=655 "lt"=> Menor o igual que
    {{url}}/api/blog_posts?id[gte]=652   --- "gt"=> Mayor o igual que
* @ApiFilter(
 *     RangeFilter::class,
 *     properties={
 *              "id"
 *     }
 * )

 Date:
    {{url}}/api/blog_posts?published[after]=2019-08-25&published[before]=2019-09-1

    {{url}}/api/blog_posts?published[strictly_after]=2019-09-04T02:00:00

     * @ApiFilter(
     *     DateFilter::class,
     *     properties={
     *              "published"
     *     }
     * )


Search:
    {{url}}/api/blog_posts?title=alice
    {{url}}/api/blog_posts?content=Cat
    {{url}}/api/blog_posts?content=Cat&title=Alice

 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
            "id": "exact",
 *          "title":"partial",
 *          "content":"partial",
 *          "author":"exact"
 *
 *     }
 * )

 Property Filter:
 {{url}}/api/blog_posts?properties[]=id&properties[]=title
 {{url}}/api/blog_posts?properties[]=id

 whiteList indica las propiedades que puede agregar o quitar en el request.
 parameterName: indica el nombre de la propiedad que pondremos en el request.

 * @ApiFilter(
  *     PropertyFilter::class,
  *     arguments={
  *       "parameterName": "properties",
  *       "overrideDefaultProperties": false,
  *       "whitelist": {"id","author","slug","title","content"}
  *     }
  * )

#----------------------------------- Loggin ----------------------------#

Install monolog loggin

1) composer require symfony/monolog-bundle
2) Definir nuevo channels
    a) crear monolog.yaml en config/packages/monolog.yaml
        monolog:
          channels: ['token_confirmation']
3) Validar que exista el channels
    php bin/console debug:container log
    Result:
        [53] monolog.logger.token_confirmation

    -> press 53 entry

    Information for Service "monolog.logger.token_confirmation"
    ===========================================================

     ---------------- -----------------------------------
      Option           Value
     ---------------- -----------------------------------
      Service ID       monolog.logger.token_confirmation
      Class            Symfony\Bridge\Monolog\Logger
      Tags             -
      Calls            pushHandler, pushHandler
      Public           yes
      Synthetic        no
      Lazy             no
      Shared           yes
      Abstract         no
      Autowired        no
      Autoconfigured   no
     ---------------- -----------------------------------

4) Inyectar logger al servicio de  UserConfirmService
   en config/services.yaml definir:

