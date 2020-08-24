<?php require_once __SITE_PATH . '/view/_header.php'; ?>
<div class="container">
    <h1 class="display-4"> <?php echo $title; ?></h1>
</div>
<div class="container">
    <div id='document_list'>
        <dl>
            <?php
                //odmah ispisuje sve korisnikove dokumente
                //ako ga netko kasnije doda u novi dokument, prikazat će mu se bez osvježavanja stranice
                //ako ga netko ukloni s nekog dokumenta, saznat će nakon osvježavanja stranice
                if( $document_list !== null )
                    foreach ( $document_list as $document )
                    {
                        echo '<div class="link">' . "\n" .
                            '<dt class="doc-id">' . $document->id . '</dt>' . "\n" .
                            '<dd> <a href="'. __SITE_URL .'/index.php?rt=texteditor&document_id=' . $document->id . '&title=' . $document->title . '" class="alert-link">' . $document->title . '</a> </dd>' . "\n" .
                            '</div> '. "\n" ;
                    }
            ?>
        </dl>
        <div class='buttons'>
            <button id="create_new" class="btn btn-primary">Kreiraj novi dokument!</button>
        </div>
    </div>
</div>

<div id="username" hidden><?php echo $document->creator_username;?></div>

<script src="<?php echo __SITE_URL; ?>/view/home_page.js" ></script>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
