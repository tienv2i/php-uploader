<?php
namespace App\Controllers;

use App\Core\Controller;

class Home extends Controller {
    public function index (...$args) {
        $this->response->render('home/index.twig', array());
    }
}