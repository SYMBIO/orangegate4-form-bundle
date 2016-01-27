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

Allow user access javascript and css via assetic

- allow ````SymbioOrangeGateFormBundle```` in ````config.yml```` - your new configuration should probably look like this
    

    assetic:
        debug:          "%kernel.debug%"
        use_controller: false
        bundles:        [SymbioOrangeGateAdminBundle, SymbioOrangeGateFormBundle]

    
- make sure to clear your app cache (````php app/console cache:clear````)
- publish assets with assetic (````php app/console assets:install web````)
  
## Usage
You can view and specify your forms on ````admin/form/form/list```` page (accessible via menu ````Content > Form````).

Each form has fields:

- **name** - Name of form. Is used in emails and blocks (see below)
- **description** - Description of form. Is used as explanation text in email body.
- **sender email** - Email from wich are notification emails send. Leave blank if you do not want to send emails.
- **submit button text** - Text of form submit button.
- **fields** - Fields of form. See below for more info.
- **recipients** - Recipients to whom will be notification emails send.

After you specify your form, you can place it on page using Form block.

### Fields

**TODO**