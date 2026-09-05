<?php
header("Access-Control-Allow-Origin: https://abeling.ir");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	include 'config.php'; // provides $pdo, current_editor(), verify_csrf_token()

	// Authorization, the actual security boundary. Anyone can POST here
	// directly; the JWT cookie is the only thing that proves this request
	// came from a logged-in editor.
	$editor = current_editor();
	if ($editor === 'user') {
		http_response_code(403);
		echo json_encode(['error' => 'دسترسی غیرمجاز']);
		exit;
	}

	// CSRF check — confirms the request came from our own form, not a
	// forged cross-site submission riding on the editor's cookies.
	if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
		http_response_code(403);
		echo json_encode(['error' => 'درخواست نامعتبر']);
		exit;
	}

	try {
		$word      = htmlspecialchars($_POST['word'] ?? '', ENT_QUOTES, 'UTF-8');
		$translate = htmlspecialchars($_POST['translate'] ?? '', ENT_QUOTES, 'UTF-8');
		$book      = htmlspecialchars($_POST['buch'] ?? '', ENT_QUOTES, 'UTF-8');
		$lesson    = htmlspecialchars($_POST['lektion'] ?? '', ENT_QUOTES, 'UTF-8');
		$example   = htmlspecialchars($_POST['beispiel'] ?? '', ENT_QUOTES, 'UTF-8');
		$comment   = htmlspecialchars($_POST['body'] ?? '', ENT_QUOTES, 'UTF-8');

		$stmt = $pdo->prepare("INSERT INTO `editorscomments`
            (`editor`, `word`, `translate`, `book`, `lesson`, `example`, `comment`, `cdate`)
            VALUES (:editor, :word, :translate, :book, :lesson, :example, :comment, current_timestamp())");

		$stmt->bindParam(':editor', $editor, PDO::PARAM_STR);
		$stmt->bindParam(':word', $word, PDO::PARAM_STR);
		$stmt->bindParam(':translate', $translate, PDO::PARAM_STR);
		$stmt->bindParam(':book', $book, PDO::PARAM_STR);
		$stmt->bindParam(':lesson', $lesson, PDO::PARAM_STR);
		$stmt->bindParam(':example', $example, PDO::PARAM_STR);
		$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);

		$stmt->execute();

		echo json_encode([
			'success' => true,
			'id' => $pdo->lastInsertId()
		]);

		$subject = "A new edit from " . $editor . "!";
		$body = $editor . " sent a comment on " . $book . " - " . $lesson .
			". He/She says '" . $comment . "' about the word: " . $word;
		mail("webtech1@webtechie.ir", $subject, $body);
	} catch (PDOException $e) {
		error_log("api_comment.php DB error: " . $e->getMessage());
		http_response_code(500);
		echo json_encode(['error' => 'خطای دیتابیس رخ داد']);
	} catch (Exception $e) {
		error_log("api_comment.php error: " . $e->getMessage());
		http_response_code(500);
		echo json_encode(['error' => 'خطای عمومی رخ داد']);
	}

	exit;
}
