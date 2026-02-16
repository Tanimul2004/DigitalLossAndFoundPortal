<?php
// includes/functions.php
require_once __DIR__ . '/config.php';

// ===== USERS =====
function user_by_email(string $email): ?array {
  global $pdo;
  $st = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $st->execute([$email]);
  $u = $st->fetch();
  return $u ?: null;
}

function user_public_by_id(int $id): ?array {
  global $pdo;
  $st = $pdo->prepare("SELECT id,name,email,role,phone,profile_image,created_at FROM users WHERE id=?");
  $st->execute([$id]);
  $u = $st->fetch();
  return $u ?: null;
}

// ===== ITEMS =====
function items_list(array $filters = [], ?int $limit = null, int $offset = 0, bool $include_pending = false): array {
  global $pdo;
  $where = [];
  $params = [];

  if (!$include_pending) {
    $where[] = "i.status='active'";
  } else {
    // include all statuses
    $where[] = "i.status IN ('pending','active','resolved')";
  }

  if (!empty($filters['type'])) {
    $where[] = "i.type=?";
    $params[] = $filters['type'];
  }
  if (!empty($filters['category'])) {
    $where[] = "i.category=?";
    $params[] = $filters['category'];
  }
  if (!empty($filters['location'])) {
    $where[] = "i.location LIKE ?";
    $params[] = "%" . $filters['location'] . "%";
  }
  if (!empty($filters['search'])) {
    $where[] = "(i.title LIKE ? OR i.description LIKE ?)";
    $params[] = "%" . $filters['search'] . "%";
    $params[] = "%" . $filters['search'] . "%";
  }

  $sql = "SELECT i.*, u.name AS reporter_name
          FROM items i JOIN users u ON i.user_id=u.id
          WHERE " . implode(" AND ", $where) . "
          ORDER BY i.created_at DESC";

  if ($limit !== null) {
    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
  }

  $st = $pdo->prepare($sql);
  $st->execute($params);
  return $st->fetchAll();
}

function items_count(array $filters = []): int {
  global $pdo;
  $where = ["status='active'"];
  $params = [];

  if (!empty($filters['type'])) { $where[]="type=?"; $params[]=$filters['type']; }
  if (!empty($filters['category'])) { $where[]="category=?"; $params[]=$filters['category']; }
  if (!empty($filters['location'])) { $where[]="location LIKE ?"; $params[]="%" . $filters['location'] . "%"; }
  if (!empty($filters['search'])) { $where[]="(title LIKE ? OR description LIKE ?)"; $params[]="%" . $filters['search'] . "%"; $params[]="%" . $filters['search'] . "%"; }

  $st = $pdo->prepare("SELECT COUNT(*) c FROM items WHERE " . implode(" AND ", $where));
  $st->execute($params);
  return (int)($st->fetch()['c'] ?? 0);
}

function item_by_id(int $id): ?array {
  global $pdo;
  $st = $pdo->prepare("
    SELECT i.*, u.name AS reporter_name, u.email AS reporter_email, u.phone AS reporter_phone
    FROM items i JOIN users u ON i.user_id=u.id
    WHERE i.id=?
  ");
  $st->execute([$id]);
  $it = $st->fetch();
  return $it ?: null;
}

function item_create(array $data): int {
  global $pdo;
  $st = $pdo->prepare("
    INSERT INTO items (type,title,description,category,location,date_lost_found,image,status,user_id)
    VALUES (?,?,?,?,?,?,?,?,?)
  ");
  $st->execute([
    $data['type'],
    $data['title'],
    $data['description'],
    $data['category'] ?: null,
    $data['location'],
    $data['date_lost_found'],
    $data['image'] ?? null,
    $data['status'] ?? 'pending',
    $data['user_id'],
  ]);
  return (int)$pdo->lastInsertId();
}

function user_items(int $userId, ?string $type=null): array {
  global $pdo;
  $sql = "SELECT * FROM items WHERE user_id=?";
  $params = [$userId];
  if ($type) { $sql .= " AND type=?"; $params[]=$type; }
  $sql .= " ORDER BY created_at DESC";
  $st = $pdo->prepare($sql);
  $st->execute($params);
  return $st->fetchAll();
}

// ===== CLAIMS =====
function claim_create(array $data): int {
  global $pdo;
  $st = $pdo->prepare("INSERT INTO claims (item_id,user_id,claim_details,proof_docs,status) VALUES (?,?,?,?, 'pending')");
  $st->execute([$data['item_id'], $data['user_id'], $data['claim_details'], $data['proof_docs'] ?: null]);
  return (int)$pdo->lastInsertId();
}

function claims_for_item(int $itemId): array {
  global $pdo;
  $st = $pdo->prepare("
    SELECT c.*, u.name AS claimant_name, u.email AS claimant_email
    FROM claims c JOIN users u ON c.user_id=u.id
    WHERE c.item_id=?
    ORDER BY c.created_at DESC
  ");
  $st->execute([$itemId]);
  return $st->fetchAll();
}

function claims_for_user(int $userId): array {
  global $pdo;
  $st = $pdo->prepare("
    SELECT c.*, 
           i.title AS item_title, i.type AS item_type, i.image AS item_image, i.status AS item_status,
           u1.name AS reporter_name, u1.email AS reporter_email, u1.phone AS reporter_phone
    FROM claims c
    JOIN items i ON c.item_id=i.id
    JOIN users u1 ON i.user_id=u1.id
    WHERE c.user_id=?
    ORDER BY c.created_at DESC
  ");
  $st->execute([$userId]);
  return $st->fetchAll();
}

function approved_claim_for_item(int $itemId): ?array {
  global $pdo;
  $st = $pdo->prepare("
    SELECT c.*, u.name AS claimant_name, u.email AS claimant_email, u.phone AS claimant_phone
    FROM claims c
    JOIN users u ON c.user_id=u.id
    WHERE c.item_id=? AND c.status='approved'
    LIMIT 1
  ");
  $st->execute([$itemId]);
  $c = $st->fetch();
  return $c ?: null;
}

function admin_recent_actions(int $limit=8): array {
  global $pdo;
  $limit = max(1, min(25, $limit));
  $st = $pdo->prepare("
    SELECT a.*, u.name AS admin_name
    FROM admin_actions a
    JOIN users u ON a.admin_id=u.id
    ORDER BY a.timestamp DESC
    LIMIT $limit
  ");
  $st->execute();
  return $st->fetchAll();
}

function claim_by_id(int $id): ?array {
  global $pdo;
  $st = $pdo->prepare("
    SELECT c.*, i.title AS item_title, i.type AS item_type, i.user_id AS reporter_id,
           u1.name AS reporter_name, u1.email AS reporter_email, u1.phone AS reporter_phone,
           u2.name AS claimant_name, u2.email AS claimant_email, u2.phone AS claimant_phone
    FROM claims c
    JOIN items i ON c.item_id=i.id
    JOIN users u1 ON i.user_id=u1.id
    JOIN users u2 ON c.user_id=u2.id
    WHERE c.id=?
  ");
  $st->execute([$id]);
  $c = $st->fetch();
  return $c ?: null;
}

// ===== ADMIN =====
function admin_pending_items(): array {
  global $pdo;
  $st = $pdo->prepare("
    SELECT i.*, u.name AS reporter_name
    FROM items i JOIN users u ON i.user_id=u.id
    WHERE i.status='pending'
    ORDER BY i.created_at DESC
  ");
  $st->execute();
  return $st->fetchAll();
}

function admin_pending_claims(): array {
  global $pdo;
  $st = $pdo->prepare("
    SELECT c.*, i.title AS item_title, i.type AS item_type,
           u1.name AS reporter_name, u1.email AS reporter_email,
           u2.name AS claimant_name, u2.email AS claimant_email
    FROM claims c
    JOIN items i ON c.item_id=i.id
    JOIN users u1 ON i.user_id=u1.id
    JOIN users u2 ON c.user_id=u2.id
    WHERE c.status='pending'
    ORDER BY c.created_at DESC
  ");
  $st->execute();
  return $st->fetchAll();
}

function admin_log(int $adminId, string $actionType, int $targetId, ?string $details=null): void {
  global $pdo;
  $st = $pdo->prepare("INSERT INTO admin_actions (admin_id, action_type, target_id, details) VALUES (?,?,?,?)");
  $st->execute([$adminId, $actionType, $targetId, $details]);
}

function admin_item_approve(int $itemId, int $adminId): void {
  global $pdo;
  $st = $pdo->prepare("UPDATE items SET status='active', approved_at=NOW() WHERE id=? AND status='pending'");
  $st->execute([$itemId]);
  admin_log($adminId, 'ITEM_APPROVE', $itemId, 'Approved item and made it active');
}

function admin_item_reject(int $itemId, int $adminId): void {
  global $pdo;
  // keep audit, then delete
  admin_log($adminId, 'ITEM_REJECT', $itemId, 'Rejected item and removed it');
  $st = $pdo->prepare("DELETE FROM items WHERE id=? AND status='pending'");
  $st->execute([$itemId]);
}

function admin_claim_reject(int $claimId, int $adminId, string $notes=''): void {
  global $pdo;
  $st = $pdo->prepare("UPDATE claims SET status='rejected', admin_notes=?, resolved_at=NOW() WHERE id=?");
  $st->execute([$notes ?: null, $claimId]);
  admin_log($adminId, 'CLAIM_REJECT', $claimId, $notes ?: 'Rejected claim');
}

function admin_claim_approve(int $claimId, int $adminId, string $notes=''): void {
  global $pdo;
  $pdo->beginTransaction();
  try {
    $claim = claim_by_id($claimId);
    if (!$claim) { throw new RuntimeException('Claim not found'); }
    $itemId = (int)$claim['item_id'];

    // approve this claim
    $st = $pdo->prepare("UPDATE claims SET status='approved', admin_notes=?, resolved_at=NOW() WHERE id=?");
    $st->execute([$notes ?: null, $claimId]);

    // reject other claims
    $st = $pdo->prepare("UPDATE claims SET status='rejected', admin_notes='Another claim was approved', resolved_at=NOW()
                         WHERE item_id=? AND id<>? AND status='pending'");
    $st->execute([$itemId, $claimId]);

    // mark item resolved and remove from public view
    $st = $pdo->prepare("UPDATE items SET status='resolved' WHERE id=?");
    $st->execute([$itemId]);

    admin_log($adminId, 'CLAIM_APPROVE', $claimId, $notes ?: 'Approved claim; marked item resolved');
    $pdo->commit();
  } catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
  }
}
