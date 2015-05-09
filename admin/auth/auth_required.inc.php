<?php
/**
 * - Überall dort einbinden, wo ein Login notwendig sein soll
 * - Vor anderem Output, da Weiterleitung per Header
 * 
 */
 
// TODO: Login absichern
session_start(); 
session_regenerate_id();

if($_SESSION['login'] !== 'ok')
{
    header("Location: ./auth/auth.php");
}
?>
