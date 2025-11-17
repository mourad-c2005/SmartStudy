<?php
require_once "models/Section.php";

class SectionController {

    public function listSections() {
        $sections = Section::getAll();
        require "views/front/sections.php";
    }
}
