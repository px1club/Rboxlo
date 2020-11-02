<?php 
	require_once($_SERVER["DOCUMENT_ROOT"] . "/../Application/Includes.php");
?>

<!DOCTYPE HTML>

<html>
	<head>
		<?php
			build_header("Forums");
        ?>
        <link rel="stylesheet" href="<?= get_server_host() ?>/html/css/forum.min.css">
	</head>
	<body class="d-flex flex-column">
		<?php
			build_navigation_bar();
		?>

        <div class="container">
            <?php
                open_database_connection($sql);

                $statement = $sql->query("SELECT * FROM `forum_hubs`");
                foreach ($statement as $result):
            ?>
            <div class="card">
                <div class="rounded-top mdb-color purple accent-4 pt-3 pl-3 pb-3 responsive-forum-grid">
                    <div class="mb-0 d-flex">
                        <div class="white-text font-weight-bold"><?= $result["name"] ?></div>
                        <div class="white-text d-inline-flex px-3 ml-auto">Threads</div>
                        <div class="white-text d-inline-flex px-3 head-separator">Replies</div>
                        <div class="white-text d-inline-flex px-3 head-separator">Last Post</div>
                    </div>
                </div>

                <?php
                    $statement = $sql->prepare("SELECT * FROM `forum_categories` WHERE `hub` = ?");
                    $statement->execute([$result["id"]]);
                    $categories = $statement;

                    foreach ($categories as $category):
                        // Fetch the replies and posts
                        $statement = $sql->prepare("SELECT COUNT(*) FROM `forum_threads` WHERE `category` = ?");
                        $statement->execute([$category["id"]]);
                        $threads = intval($statement->rowCount());

                        $statement = $sql->prepare("SELECT COUNT(*) FROM `forum_replies` WHERE `category` = ?");
                        $statement->execute([$category["id"]]);
                        $replies = intval($statement->rowCount());

                        // Now output
                ?>

                <?php
                    endforeach;
                ?>

            </div>
            <?php
                endforeach;

                close_database_connection($sql, $statement);
            ?>
		</div>

		<?php
			build_footer();
		?>
	</body>
</html>
