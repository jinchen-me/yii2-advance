<?php

namespace console\controllers;

use common\models\User;
use Faker\Factory;
use Yii;
use yii\console\Controller;
use yii\db\Connection;
use yii\di\Instance;
use yii\helpers\VarDumper;

class FakerController extends Controller
{
    /**
     * @var string|Connection
     */
    public $db = 'db';
    /**
     * @var string
     */
    public $language;
    /**
     * @var \Faker\Generator
     */
    private $generator;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->db = Instance::ensure($this->db, Connection::class);
    }

    /**
     * @return \Faker\Generator
     */
    public function generator()
    {
        if ($this->generator === null) {
            $language = $this->language === null ? Yii::$app->language : $this->language;
            $this->generator = Factory::create(str_replace('-', '_', $language));
        }
        return $this->generator;
    }

    public function actionUser($count = 1)
    {
        for ($i = 0; $i < $count; $i++) {
            $faker = $this->generator();
            $timestamp = time();
            $row = [
                'username' => $faker->userName,
                'auth_key' => Yii::$app->security->generateRandomString(),
                'password_hash' => '$2y$13$IpbH0O72.7cTgIOC2OvoHuQUky4Q3WkMI6CRjg.1IFxNyP6.VbdkG',
                'password_reset_token' => Yii::$app->security->generateRandomString(),
                'email' => $faker->unique()->email,
                'status' => User::STATUS_ACTIVE,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
            $this->db->createCommand()->insert('user', $row)->execute();
        }
    }
}
