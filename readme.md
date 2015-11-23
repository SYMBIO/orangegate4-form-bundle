# Symbio OrangeGate Form Bundle

Allows users to create simple forms as Page blocks.
 
**Work in progress... - this bundle is not usable yet**

## Instalation
Prefered way is via composer:

    composer require symbio/orangegate4-form-bundle

Then add bundle initialization to ````AppKernel.php````

    new Symbio\OrangeGate\FormBundle\SymbioOrangeGateFormBundle()
    
## Configuration
Register form block by adding it to ````sonata/sonata_block.yml```` like this

    sonata_block:
        blocks:
            orangegate.form.block.form:

Register controller for handling form submits by adding it to ````routing.yml````

    symbio_orangegate_form:
        resource: "@SymbioOrangeGateFormBundle/Controller/FormController.php"
        type:     annotation

To display Form admin at dashboard add ````orangegeate.form.admin.form```` to ````/sonata/sonata_admin.yml````

    sonata_admin:
        dashboard:
            groups:
                sonata.admin.group.content:
                    items:
                        - orangegate.form.admin.form # this line is the only one you probably do not have already

Allow user access javascript via assetic

- allow ````SymbioOrangeGateFormBundle```` in ````config.yml```` - your new configuration should probably look like this


    assetic:
        debug:          "%kernel.debug%"
        use_controller: false
        bundles:        [SymbioOrangeGateAdminBundle, SymbioOrangeGateFormBundle]

    
- make sure to clear your app cache (````php app/console cache:clear````)
- publish assets with assetic (````php app/console assets:install web````)
  
## Usage
**TODO**