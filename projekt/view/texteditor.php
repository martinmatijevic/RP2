<?php require_once __SITE_PATH . '/view/_header.php'; ?>
<div class="container">
	<h2> <?php echo $title; ?> </h2>
	<div id="editor">
		<div id="text-editor" class="text_editor">
			<div class="uredivo">
			<?php
				if( $document->content !== null )	//ako jos nema poruka, $document->content nije array nego null
					foreach ( json_decode($document->content) as $line )
					{
						if( ( $line->is_locked === 1 ) && ( $line->editing !== $username ) )
							echo '<p id="' . $document->id . '_' . $line->id_line . '" class="locked">+ ' .
							$line->content .
							'</p>';
						else
							echo '<p id="' . $document->id . '_' . $line->id_line . '">+ ' .
							$line->content .
							'</p>';
					}
			?>
			</div>
		</div>
	</div>

	<div id="chat" class="chat">
		<div>
			Dodaj collaboratora sa usernameom:
			<input class="form-control" type="text" id="txt_dodaj"><button id="btn_dodaj" class="btn btn-primary btn-lg btn-block" >Dodaj!</button>
			<br>
			<div id="add_collab" class="add_collab">
			</div>
		</div>
		<br>
		<hr>
		<br>
		<h2>Chat!</h2>
		<div id="messages-container" style="max-height: 50vh; overflow-x: hidden; overflow-y: scroll;">
			<?php
				if( $messages_list !== null )	//ako jos nema poruka, $messages_list nije array nego null
					foreach ( $messages_list as $message )
					{
						echo '<div class="message-id" id="id_' . $message->id . '">'
							. '<small>' . $message->time . '</small><br><b>'
							. $message->username . '</b>: '
							. htmlspecialchars( urldecode($message->content) )
							. '</div>';
					}

			?>
		</div>

		<br />
		<input class="form-control" type="text" id="txt">
		<button id="btn" class="btn btn-primary btn-lg btn-block">Pošalji</button>
	</div>
</div>


<div id="username" hidden><?php echo $username;?></div>
	<script src="<?php echo __SITE_URL; ?>/view/chat.js"></script>
	<script src="<?php echo __SITE_URL; ?>/view/texteditor.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.js"></script>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
