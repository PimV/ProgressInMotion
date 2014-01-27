<?php

class BiographyController extends MyController {

    public function IndexAction() {
        $this->setViewValue('pageTitle', 'Biography');
    }

}
