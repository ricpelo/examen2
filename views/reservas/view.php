<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Reservas */

$this->title = "Vuelo {$model->vuelo->codigo}, asiento {$model->asiento}";
$this->params['breadcrumbs'][] = ['label' => 'Reservas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reservas-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'vuelo.codigo',
            'asiento',
            'created_at:datetime:Reservado el',
        ],
    ]) ?>

</div>
