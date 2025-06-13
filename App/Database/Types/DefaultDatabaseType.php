<?php 

namespace App\Database\Types;

use PDO;
use DateTime;
use App\Utils\Image;
use RuntimeException;
use JsonSerializable;
use App\Database\Database;
use InvalidArgumentException;

class DefaultDatabaseType implements JsonSerializable
{
    protected PDO $pdo;
    protected int|string $id;
    protected string $table;

    /**
     * Constructeur de la classe DefaultDatabaseType.
     *
     * @throws PDOException Si le lieu n'existe pas dans la base
     */
    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }

    /**
     * Met à jour les champs spécifiés de l'utilisateur dans la base de données.
     *
     * @param array $fields Champs à mettre à jour (clé => valeur)
     * @throws PDOException En cas d'erreur SQL
     * @throws InvalidArgumentException Si les champs fournis sont invalides
     */
    public function update(array $data): self
    {
        foreach ($data as $key => $value) {            
            if (is_null($value)){
                continue;
            }
            
            if (is_string($value)){ 
                $value = trim($value); 
            }

            $setter = 'set' . ucfirst($key);
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            } elseif (property_exists($this, $key)) {
                throw new InvalidArgumentException('Modification interdite ou non permise de : ' . self::class . '.' . $key);
                // $this->$key = $value; 
            }
        }
        $this->save();
        return $this;
    }

    /**
     * Sauvegarde l'utilisateur dans la base de données (insertion ou mise à jour).
     *
     * @throws PDOException En cas d'erreur SQL (ex. violation d'unicité sur email ou nom_utilisateur)
     */
    public function save(): void {}

    /**
     * Formate la valeur d'un champ pour la requête SQL.
     *
     * @param mixed $value Valeur du champ
     * @return mixed Valeur formatée
     */
    protected function formatFieldForQuery($value)
    {
        if ($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:s');
        } elseif ($value instanceof Sexe || $value instanceof TypeUtilisateur) {
            return $value->value;
        } elseif ($value instanceof Image) {
            return $value->getFilePath();
        } elseif (is_bool($value)) {
            return (int)$value;
        }
        return $value;
    }

    public function delete(): bool
    {
        if (!property_exists($this, 'id') || !isset($this->id)) {
            throw new RuntimeException('Cannot delete: missing primary key "id".');
        }

        if (!property_exists($this, 'table') || empty($this->table)) {
            throw new RuntimeException('Cannot delete: missing table name.');
        }

        $sql = "DELETE FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute(['id' => $this->id]);
    }

}