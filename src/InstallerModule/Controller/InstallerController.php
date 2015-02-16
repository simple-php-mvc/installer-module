<?php

namespace InstallerModule\Controller;

use MVC\Controller\Controller,
	MVC\MVC;

class InstallerController extends Controller
{

	function index(MVC $mvc)
	{
		return '<title>Simple PHP MVC Installer</title>';
	}

}