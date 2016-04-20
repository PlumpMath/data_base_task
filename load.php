<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once ('model.php');



$thread = new LoadThread();
$thread->start();
$thread->join();
$role = $thread->getUserRole();
if ($role == R_ADMIN)
    header('Location: admin_work.php');
else
    header('Location: inspector_work.php');
exit();
/* enter a synchronization block with thread */
$thread->synchronized(function() use($thread) {
    /* change predicate synchronized */
    $thread->cond = TRUE;
    /* then send notification */
    $thread->notify();
});



