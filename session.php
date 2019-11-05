<?php
// ESTE SCRIPT TEM COMO FINALIDADE INICIAR E AUTENTICAR O USUÁRIO NA SESSÃO (ANTI SESSION HIJACK)
$currentSessionBrowser = (isset($_SERVER['HTTP_USER_AGENT']))? $_SERVER['HTTP_USER_AGENT'] : '';
session_name('session_'.strtoupper(md5('seg'.$currentSessionBrowser)));
@session_start() or die('Esta página requer que os cookies do seu navegador estejam ativados!');
/*
if((!isset($_SESSION['secure']['HTTP_USER_AGENT']) || !isset($_SERVER['HTTP_USER_AGENT']) || $_SESSION['secure']['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT'])){
    // falha na autenticação da sessão
    $_SESSION = Array();
}
$_SESSION['secure']['HTTP_USER_AGENT'] = (isset($_SERVER['HTTP_USER_AGENT']))? $_SERVER['HTTP_USER_AGENT'] : '';
*/