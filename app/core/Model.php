<?php
/**
 * Clase base para todos los modelos
 */
abstract class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct()
    {
        require_once __DIR__ . '/App.php';
        $this->db = App::getInstance()->getDatabase();
    }

    /**
     * Obtiene todos los registros
     */
    public function all()
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene un registro por ID
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Obtiene registros con paginación
     */
    public function paginate($page = 1, $perPage = ITEMS_PER_PAGE)
    {
        $offset = ($page - 1) * $perPage;
        
        // Contar total de registros
        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute();
        $total = $countStmt->fetch()['total'];
        
        // Obtener registros
        $sql = "SELECT * FROM {$this->table} LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }

    /**
     * Crea un nuevo registro
     */
    public function create($data)
    {
        $data = $this->filterFillable($data);
        
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $fieldList = implode(', ', $fields);
        
        $sql = "INSERT INTO {$this->table} ({$fieldList}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        
        return $this->db->lastInsertId();
    }

    /**
     * Actualiza un registro
     */
    public function update($id, $data)
    {
        $data = $this->filterFillable($data);
        
        $fields = array_keys($data);
        $setClause = implode(' = :', $fields) . ' = :' . implode(', ', $fields);
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :id";
        $data['id'] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Elimina un registro
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Busca registros por condiciones
     */
    public function where($conditions, $params = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$conditions}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Busca un registro por condiciones
     */
    public function whereFirst($conditions, $params = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$conditions} LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Ejecuta una consulta personalizada
     */
    public function query($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Ejecuta una consulta personalizada que devuelve un solo registro
     */
    public function queryFirst($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Filtra los datos por los campos fillable
     */
    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Obtiene el último ID insertado
     */
    public function getLastInsertId()
    {
        return $this->db->lastInsertId();
    }

    /**
     * Inicia una transacción
     */
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    /**
     * Confirma una transacción
     */
    public function commit()
    {
        return $this->db->commit();
    }

    /**
     * Revierte una transacción
     */
    public function rollback()
    {
        return $this->db->rollback();
    }

    /**
     * Verifica si existe un registro
     */
    public function exists($id)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch()['count'] > 0;
    }

    /**
     * Cuenta registros
     */
    public function count($conditions = null, $params = [])
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if ($conditions) {
            $sql .= " WHERE {$conditions}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'];
    }

    /**
     * Obtiene la conexión a la base de datos
     */
    public function getDb()
    {
        return $this->db;
    }
} 