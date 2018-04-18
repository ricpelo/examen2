<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * VuelosSearch represents the model behind the search form of `app\models\Vuelos`.
 */
class VuelosSearch extends Vuelos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo', 'origen.codigo', 'destino.codigo'], 'filter', 'filter' => 'mb_strtoupper'],
            [['salida', 'llegada', 'compania.denominacion'], 'safe'],
            [['plazas', 'precio', 'plazas_libres'], 'number'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'origen.codigo',
            'destino.codigo',
            'compania.denominacion',
            'plazas_libres',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Vuelos::find()->where('salida > localtimestamp');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->joinWith(['origen o', 'destino d', 'compania c'])
            ->addGroupBy('o.codigo, d.codigo, c.denominacion')
            ->having('plazas - COUNT(r.id) > 0');

        $dataProvider->sort->attributes['plazas_libres'] = [
            'asc' => ['plazas_libres' => SORT_ASC],
            'desc' => ['plazas_libres' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['origen.codigo'] = [
            'asc' => ['o.codigo' => SORT_ASC],
            'desc' => ['o.codigo' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['destino.codigo'] = [
            'asc' => ['d.codigo' => SORT_ASC],
            'desc' => ['d.codigo' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['compania.denominacion'] = [
            'asc' => ['c.denominacion' => SORT_ASC],
            'desc' => ['c.denominacion' => SORT_DESC],
        ];

        // grid filtering conditions
        $query->andFilterWhere([
            'vuelos.codigo' => $this->codigo,
            'o.codigo' => $this->getAttribute('origen.codigo'),
            'd.codigo' => $this->getAttribute('destino.codigo'),
            'salida' => $this->salida,
            'llegada' => $this->llegada,
            'plazas' => $this->plazas,
            'precio' => $this->precio,
        ]);

        $query->andFilterWhere([
            'ilike',
            'c.denominacion',
            $this->getAttribute('compania.denominacion'),
        ]);

        $query->andFilterHaving([
            'plazas - COUNT(r.id)' => $this->plazas_libres,
        ]);

        return $dataProvider;
    }
}
