 <?php

class RegistryController extends Yaf\Controller_Abstract
{


    public function testAction()
    {
        Yaf\Registry::set('test', ['username' => 'Peter']);

        var_dump(Yaf\Registry::get('test'));

        var_dump(Yaf\Registry::has('test'));

        var_dump(Yaf\Registry::del('test'));

        var_dump(Yaf\Registry::has('test'));

        var_dump(Yaf\Registry::get('phpcasts')); // NULL

        var_dump(Yaf\Registry::get('config'));
    }
}