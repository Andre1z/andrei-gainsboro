<?php
$html = "
        <style>
        	#admin-content{margin:auto;width:100%;height:90%;}
        	.admin-section{height:90%;}
        </style>
        <div class='login-box'>
                    <img src='gainsboro.png' alt='Logo'>
                    <h2>Acceso al Panel</h2>
                    $message
                    <form method='post' action='?action=do_login'>
                        <label>Usuario:</label>
                        <input type='text' name='username' required>
                        <label>Contraseña:</label>
                        <input type='password' name='password' required>
                        <button type='submit'>Acceder</button>
                    </form>
                 </div>";
        renderAdmin($html, false);
?>
