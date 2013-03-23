<?php
                
if (file_exists("no.install")){
    header("Location:/@install/");
    exit;
}

header("Location:/admin/");
exit;