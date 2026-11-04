<?php

namespace App\Domain\Configuracao\Notification\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $id
 * @property mixed $type
 * @property mixed $notifiable_id
 * @property mixed $created_at
 * @property mixed $notifiable_type
 * @property mixed $data
 * @property mixed $read_at
 * @property mixed $updated_at
 */
class Notification extends Model
{
    protected $table = 'notifications';

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getNotifiableId()
    {
        return $this->notifiable_id;
    }

    /**
     * @param mixed $notifiable_id
     */
    public function setNotifiableId($notifiable_id)
    {
        $this->notifiable_id = $notifiable_id;
    }

    /**
     * @return mixed
     */
    public function getNotifiableType()
    {
        return $this->notifiable_type;
    }

    /**
     * @param mixed $notifiable_type
     */
    public function setNotifiableType($notifiable_type)
    {
        $this->notifiable_type = $notifiable_type;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getReadAt()
    {
        return $this->read_at;
    }

    /**
     * @param mixed $read_at
     */
    public function setReadAt($read_at)
    {
        $this->read_at = $read_at;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }
}
