![AWHS Logo](https://nicolasmeloni.ovh/images/awhspanel.png)

# CrmBundle
CrmBundle of AWHSPanel
This package provides a basic ticket system.

## Requirements
* Have installed the [foundation](https://github.com/TheGrimmChester/AWHSPanel/blob/master/README.md)  
* Have installed the [UserBundle](https://github.com/TheGrimmChester/UserBundle/blob/master/README.md)

## Installation
1. Clone this bundle inside `src/AWHS/` directory.
2. Enable the bundle in the kernel by adding the following line right after `//AWHS Bundles` in `app/AppKernel.php` file:  
`new AWHS\CrmBundle\AWHSCrmBundle(),`
3. Append the following configuration to the `app/config.yml` file:  
```yaml
#CrmBundle bbcode formatter
fm_bbcode:
    filter_sets:
        default:
            strict: false # if you want to parse attr values without quotes
            locale: ru
            xhtml: true
            filters: [ default, block, code, email, image, list, quote, text, url ]
```
4. Append the following configuration to the `app/routing.yml` file:  
```yaml
awhs_crm:
    resource: "@AWHSCrmBundle/Resources/config/routing.yml"
    prefix:   /
```
5. Add the following code to the `src/AWHS/UserBundle/Entity/User.php` file:
```yaml
/**
 * @ORM\OneToMany(targetEntity="AWHS\CrmBundle\Entity\Ticket", mappedBy="author")
 * @ORM\OrderBy({"id" = "DESC"})
 */
private $tickets;

/**
 * Get tickets
 *
 * @return \Doctrine\Common\Collections\Collection
 */
public function getTickets()
{
    return $this->tickets;
}
```
6. Update database & clear cache: `php bin/console doctrine:schema:update --force; php bin/console cache:clear; php bin/console cache:clear --env=prod`  
You may have to set permissions back to www-data `chown -R www-data:www-data /usr/local/awhspanel/panel/*`
7. Load data fixtures: `php bin/console doctrine:fixtures:load --fixtures=src/AWHS/CrmBundle/DataFixtures/ORM --append`

## TODO
- [ ] Multilingual
- [ ] Add category dropdown to choose when creating a ticket.
- [ ] Refactoring old code.