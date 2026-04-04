<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function db(): PDO
{
    global $pdo;

    return $pdo;
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function sanitize(mixed $value): string
{
    return trim((string) $value);
}

function base_url(string $path = ''): string
{
    return BASE_URL . ltrim($path, '/');
}

function redirect(string $path): void
{
    $destination = preg_match('~^https?://~', $path) ? $path : base_url($path);
    header('Location: ' . $destination);
    exit;
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return ($_SESSION['user_role'] ?? '') === 'admin';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect('public/login.php');
    }
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        redirect('public/login.php');
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(mixed $token): bool
{
    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function flash_get(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $flash;
}

function categories(): array
{
    return [
        'electronics' => 'Electronics',
        'documents' => 'Documents',
        'jewelry' => 'Jewelry',
        'clothing' => 'Clothing',
        'bags' => 'Bags & Wallets',
        'keys' => 'Keys',
        'books' => 'Books',
        'other' => 'Other',
    ];
}

function statusBadge(string $status): string
{
    return match ($status) {
        'pending' => 'bg-amber-500/15 text-amber-300 border border-amber-500/20',
        'active' => 'bg-blue-500/15 text-blue-300 border border-blue-500/20',
        'approved', 'resolved' => 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/20',
        'rejected' => 'bg-red-500/15 text-red-300 border border-red-500/20',
        default => 'bg-slate-500/15 text-slate-300 border border-slate-500/20',
    };
}

function itemTypeBadge(string $type): string
{
    return $type === 'lost'
        ? 'bg-red-500/15 text-red-300 border border-red-500/20'
        : 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/20';
}

function getUserByEmail(string $email): ?array
{
    $statement = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $statement->execute([$email]);

    return $statement->fetch() ?: null;
}

function getUserById(int $id): ?array
{
    $statement = db()->prepare(
        'SELECT id, name, email, role, phone, profile_image, created_at FROM users WHERE id = ?'
    );
    $statement->execute([$id]);

    return $statement->fetch() ?: null;
}

function loginUser(array $user): void
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_profile_image'] = $user['profile_image'] ?? null;
}

function createUser(string $name, string $email, string $password, string $phone = ''): int
{
    $statement = db()->prepare(
        'INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)'
    );
    $statement->execute([
        $name,
        $email,
        password_hash($password, PASSWORD_DEFAULT),
        $phone ?: null,
    ]);

    return (int) db()->lastInsertId();
}

function countRows(string $table, string $where = '1=1'): int
{
    return (int) db()->query("SELECT COUNT(*) FROM {$table} WHERE {$where}")->fetchColumn();
}

function uploadImage(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? 1) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Image upload failed.');
    }

    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        throw new RuntimeException('Image must be under 5MB.');
    }

    $mime = mime_content_type($file['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only JPG, PNG, GIF or WEBP images are allowed.');
    }

    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0775, true);
    }

    $fileName = uniqid('item_', true) . '.' . $allowed[$mime];
    $targetPath = UPLOAD_PATH . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('Could not save uploaded image.');
    }

    return $fileName;
}

function createItem(array $data): int
{
    $statement = db()->prepare(
        'INSERT INTO items (
            type,
            title,
            description,
            category,
            brand,
            color,
            serial_number,
            unique_marks,
            location,
            latitude,
            longitude,
            date_lost_found,
            image,
            status,
            user_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );

    $statement->execute([
        $data['type'],
        $data['title'],
        $data['description'],
        $data['category'] ?: null,
        $data['brand'] ?: null,
        $data['color'] ?: null,
        $data['serial_number'] ?: null,
        $data['unique_marks'] ?: null,
        $data['location'],
        $data['latitude'] ?: null,
        $data['longitude'] ?: null,
        $data['date_lost_found'],
        $data['image'] ?: null,
        $data['status'] ?? 'pending',
        $data['user_id'],
    ]);

    return (int) db()->lastInsertId();
}

function getItems(array $filters = [], ?int $limit = null, int $offset = 0, bool $includeAll = false): array
{
    $where = [];
    $params = [];

    if (!$includeAll) {
        $where[] = "i.status = 'active'";
    }

    foreach (['status', 'type', 'category'] as $field) {
        if (!empty($filters[$field])) {
            $where[] = "i.{$field} = ?";
            $params[] = $filters[$field];
        }
    }

    if (!empty($filters['user_id'])) {
        $where[] = 'i.user_id = ?';
        $params[] = $filters['user_id'];
    }

    if (!empty($filters['location'])) {
        $where[] = 'i.location LIKE ?';
        $params[] = '%' . $filters['location'] . '%';
    }

    if (!empty($filters['search'])) {
        $where[] = '(
            i.title LIKE ?
            OR i.description LIKE ?
            OR i.location LIKE ?
            OR i.brand LIKE ?
            OR i.color LIKE ?
            OR i.serial_number LIKE ?
        )';
        for ($i = 0; $i < 6; $i++) {
            $params[] = '%' . $filters['search'] . '%';
        }
    }

    $sql = 'SELECT i.*, u.name AS reporter_name FROM items i JOIN users u ON u.id = i.user_id';

    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= ' ORDER BY i.created_at DESC';

    if ($limit !== null) {
        $sql .= ' LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;
    }

    $statement = db()->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll();
}

function getItemById(int $id): ?array
{
    $statement = db()->prepare(
        'SELECT i.*, u.name AS reporter_name, u.email AS reporter_email, u.phone AS reporter_phone
         FROM items i
         JOIN users u ON u.id = i.user_id
         WHERE i.id = ?
         LIMIT 1'
    );
    $statement->execute([$id]);

    return $statement->fetch() ?: null;
}

function getUserItems(int $userId, ?string $type = null): array
{
    $filters = ['user_id' => $userId];
    if ($type !== null) {
        $filters['type'] = $type;
    }

    return getItems($filters, null, 0, true);
}

function calculateClaimMatchScore(array $item, array $claim): array
{
    $score = 0;
    $summary = [];
    $equalsIgnoreCase = static fn (string $a, string $b): bool => strtolower(trim($a)) === strtolower(trim($b));

    if (!empty($item['serial_number']) && !empty($claim['claimed_serial']) && $equalsIgnoreCase($item['serial_number'], $claim['claimed_serial'])) {
        $score += 50;
        $summary[] = 'Serial matched';
    }

    if (!empty($item['brand']) && !empty($claim['claimed_brand']) && $equalsIgnoreCase($item['brand'], $claim['claimed_brand'])) {
        $score += 10;
        $summary[] = 'Brand matched';
    }

    if (!empty($item['color']) && !empty($claim['claimed_color']) && $equalsIgnoreCase($item['color'], $claim['claimed_color'])) {
        $score += 10;
        $summary[] = 'Color matched';
    }

    if (!empty($item['location']) && !empty($claim['claimed_location'])) {
        similar_text(strtolower($item['location']), strtolower($claim['claimed_location']), $percent);
        if ($percent >= 70) {
            $score += 15;
            $summary[] = 'Location matched';
        } elseif ($percent >= 40) {
            $score += 8;
            $summary[] = 'Location partially matched';
        }
    }

    if (!empty($item['date_lost_found']) && !empty($claim['claimed_date'])) {
        $days = abs((strtotime($item['date_lost_found']) - strtotime($claim['claimed_date'])) / 86400);
        if ($days == 0) {
            $score += 10;
            $summary[] = 'Date matched';
        } elseif ($days <= 2) {
            $score += 5;
            $summary[] = 'Date close';
        }
    }

    if (!empty($item['unique_marks']) && !empty($claim['identifying_marks'])) {
        similar_text(strtolower($item['unique_marks']), strtolower($claim['identifying_marks']), $percent);
        if ($percent >= 50) {
            $score += 10;
            $summary[] = 'Unique marks similar';
        }
    }

    if (!empty($claim['claim_details'])) {
        similar_text(strtolower($item['description']), strtolower($claim['claim_details']), $percent);
        if ($percent >= 30) {
            $score += 5;
            $summary[] = 'Description partially matched';
        }
    }

    return [
        'score' => min(100, $score),
        'summary' => $summary ? implode(', ', $summary) : 'Low information match',
    ];
}

function createClaim(array $data): int
{
    $item = getItemById((int) $data['item_id']);
    if (!$item) {
        throw new RuntimeException('Item not found.');
    }

    $match = calculateClaimMatchScore($item, $data);

    $statement = db()->prepare(
        'INSERT INTO claims (
            item_id,
            user_id,
            claim_details,
            proof_docs,
            claimed_brand,
            claimed_color,
            claimed_serial,
            claimed_location,
            claimed_date,
            identifying_marks,
            match_score,
            match_summary
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );

    $statement->execute([
        $data['item_id'],
        $data['user_id'],
        $data['claim_details'],
        $data['proof_docs'] ?: null,
        $data['claimed_brand'] ?: null,
        $data['claimed_color'] ?: null,
        $data['claimed_serial'] ?: null,
        $data['claimed_location'] ?: null,
        $data['claimed_date'] ?: null,
        $data['identifying_marks'] ?: null,
        $match['score'],
        $match['summary'],
    ]);

    return (int) db()->lastInsertId();
}

function getItemClaims(int $itemId): array
{
    $statement = db()->prepare(
        'SELECT c.*, u.name AS claimant_name, u.email AS claimant_email
         FROM claims c
         JOIN users u ON u.id = c.user_id
         WHERE c.item_id = ?
         ORDER BY c.match_score DESC, c.created_at DESC'
    );
    $statement->execute([$itemId]);

    return $statement->fetchAll();
}

function getUserClaims(int $userId): array
{
    $statement = db()->prepare(
        'SELECT c.*, i.title AS item_title, i.type AS item_type, i.image AS item_image
         FROM claims c
         JOIN items i ON i.id = c.item_id
         WHERE c.user_id = ?
         ORDER BY c.created_at DESC'
    );
    $statement->execute([$userId]);

    return $statement->fetchAll();
}

function getClaimById(int $id): ?array
{
    $statement = db()->prepare(
        'SELECT c.*, i.title AS item_title, i.type AS item_type, i.id AS real_item_id, u.name AS claimant_name
         FROM claims c
         JOIN items i ON i.id = c.item_id
         JOIN users u ON u.id = c.user_id
         WHERE c.id = ?
         LIMIT 1'
    );
    $statement->execute([$id]);

    return $statement->fetch() ?: null;
}

function approveClaimAndDeleteItem(int $claimId, int $adminId): void
{
    $claim = getClaimById($claimId);
    if (!$claim) {
        throw new RuntimeException('Claim not found.');
    }

    $itemId = (int) $claim['item_id'];
    db()->beginTransaction();

    try {
        db()->prepare("UPDATE claims SET status = 'approved', resolved_at = NOW() WHERE id = ?")
            ->execute([$claimId]);

        db()->prepare(
            "UPDATE claims
             SET status = 'rejected', resolved_at = NOW(), admin_notes = 'Another claimant was approved'
             WHERE item_id = ? AND id <> ? AND status = 'pending'"
        )->execute([$itemId, $claimId]);

        db()->prepare(
            'INSERT INTO admin_actions (admin_id, action_type, target_id, details) VALUES (?, ?, ?, ?)'
        )->execute([
            $adminId,
            'approve_claim_delete_item',
            $claimId,
            'Approved claim after reviewing match score; item deleted',
        ]);

        db()->prepare('DELETE FROM items WHERE id = ?')->execute([$itemId]);
        db()->commit();
    } catch (Throwable $e) {
        db()->rollBack();
        throw $e;
    }
}

function rejectClaim(int $claimId, int $adminId, string $notes = 'Rejected by admin'): void
{
    db()->prepare(
        "UPDATE claims SET status = 'rejected', admin_notes = ?, resolved_at = NOW() WHERE id = ?"
    )->execute([$notes, $claimId]);

    db()->prepare(
        'INSERT INTO admin_actions (admin_id, action_type, target_id, details) VALUES (?, ?, ?, ?)'
    )->execute([$adminId, 'reject_claim', $claimId, $notes]);
}

function setItemStatus(int $itemId, string $status, int $adminId): void
{
    if (!in_array($status, ['pending', 'active', 'resolved', 'rejected'], true)) {
        throw new RuntimeException('Invalid status');
    }

    db()->prepare(
        'UPDATE items
         SET status = ?,
             approved_at = IF(? = "active", NOW(), approved_at),
             resolved_at = IF(? = "resolved", NOW(), resolved_at)
         WHERE id = ?'
    )->execute([$status, $status, $status, $itemId]);

    db()->prepare(
        'INSERT INTO admin_actions (admin_id, action_type, target_id, details) VALUES (?, ?, ?, ?)'
    )->execute([$adminId, 'item_status_' . $status, $itemId, 'Item set to ' . $status]);
}

function approvedClaimForUser(int $itemId, int $userId): bool
{
    $statement = db()->prepare(
        "SELECT COUNT(*) FROM claims WHERE item_id = ? AND user_id = ? AND status = 'approved'"
    );
    $statement->execute([$itemId, $userId]);

    return (int) $statement->fetchColumn() > 0;
}

function canViewReporterInfo(array $item, array $claims): bool
{
    if (isAdmin()) {
        return true;
    }

    if (!isLoggedIn()) {
        return false;
    }

    foreach ($claims as $claim) {
        if ((int) $claim['user_id'] === (int) $_SESSION['user_id'] && $claim['status'] === 'approved') {
            return true;
        }
    }

    return false;
}
