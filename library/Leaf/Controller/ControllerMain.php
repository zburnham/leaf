<?php
/**
 * MainController.class.php
 * "Main" controller for our application.
 * 
 * @author zburnham
 * @version 0.0.1
 */
class Controller_Main extends Controller
{   
    public function indexAction()
    {
        $this->view->foo = "foo!";
    }
}