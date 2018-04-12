<?php

namespace app\models;

/**
 * This is the model class for table "v_vuelos".
 *
 * @property int $id
 * @property string $codigo
 * @property int $origen_id
 * @property int $destino_id
 * @property int $compania_id
 * @property string $salida
 * @property string $llegada
 * @property string $plazas
 * @property string $precio
 * @property string $plazas_libres
 */
class VVuelos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_vuelos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'origen_id', 'destino_id', 'compania_id'], 'default', 'value' => null],
            [['id', 'origen_id', 'destino_id', 'compania_id'], 'integer'],
            [['salida', 'llegada'], 'safe'],
            [['plazas', 'precio', 'plazas_libres'], 'number'],
            [['codigo'], 'string', 'max' => 6],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Codigo',
            'origen_id' => 'Origen ID',
            'destino_id' => 'Destino ID',
            'compania_id' => 'Compania ID',
            'salida' => 'Salida',
            'llegada' => 'Llegada',
            'plazas' => 'Plazas',
            'precio' => 'Precio',
            'plazas_libres' => 'Plazas Libres',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReservas()
    {
        return $this->hasMany(Reservas::className(), ['vuelo_id' => 'id'])->inverseOf('vuelo');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrigen()
    {
        return $this->hasOne(Aeropuertos::className(), ['id' => 'origen_id'])->inverseOf('vuelosOrigen');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestino()
    {
        return $this->hasOne(Aeropuertos::className(), ['id' => 'destino_id'])->inverseOf('vuelosDestino');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompania()
    {
        return $this->hasOne(Companias::className(), ['id' => 'compania_id'])->inverseOf('vuelos');
    }
}
