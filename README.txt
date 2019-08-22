#-----------------------Crear proyecto Symfony 4 - EasyAdmin - Api Platform -----------------------#

Se asume que esta instalado:
    php version > 7.3,
    composer

NOTA: Las URL que veran a continuacion se tratan symfony en docker con php 7.3 expuesto en puerto :83

1) composer create-project symfony/skeleton project -vvv
2) composer require annotations
3) composer require symfony/orm-pack
4) composer require symfony/maker-bundle --dev

    Prueba: http://localhost:83/s4apiPlatform/project/
            http://localhost:83/s4apiPlatform/project/public/index.php/blog/1

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
    http://localhost:83/s4apiPlatform/project/public/index.php/admin/?action=list&entity=BlogPost

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