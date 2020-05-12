<?php

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 27.01.2020
 * Time: 10:10
 */
class Boosterpack_model extends CI_Emerald_Model
{
    const CLASS_TABLE = 'boosterpack';


    /** @var float Цена бустерпака */
    protected $price;
    /** @var float Банк, который наполняется  */
    protected $bank;

    /** @var string */
    protected $time_created;
    /** @var string */
    protected $time_updated;

    /**
     * @return float
     */
    public function get_price(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     *
     * @return bool
     */
    public function set_price(float $price)
    {
        $this->price = $price;
        return $this->save('price', $price);
    }

    /**
     * @return float
     */
    public function get_bank(): float
    {
        return $this->bank;
    }

    /**
     * @param float $bank
     *
     * @return bool
     */
    public function set_bank(float $bank)
    {
        $this->bank = $bank;
        return $this->save('bank', $bank);
    }

    /**
     * @return string
     */
    public function get_time_created(): string
    {
        return $this->time_created;
    }

    /**
     * @param string $time_created
     *
     * @return bool
     */
    public function set_time_created(string $time_created)
    {
        $this->time_created = $time_created;
        return $this->save('time_created', $time_created);
    }

    /**
     * @return string
     */
    public function get_time_updated(): string
    {
        return $this->time_updated;
    }

    /**
     * @param string $time_updated
     *
     * @return bool
     */
    public function set_time_updated(string $time_updated)
    {
        $this->time_updated = $time_updated;
        return $this->save('time_updated', $time_updated);
    }

    function __construct($id = NULL)
    {
        parent::__construct();

        $this->set_id($id);
    }

    public function reload(bool $for_update = FALSE)
    {
        parent::reload($for_update);

        return $this;
    }

    public static function create(array $data)
    {
        App::get_ci()->s->from(self::CLASS_TABLE)->insert($data)->execute();
        return new static(App::get_ci()->s->get_insert_id());
    }

    public function delete()
    {
        $this->is_loaded(TRUE);
        App::get_ci()->s->from(self::CLASS_TABLE)->where(['id' => $this->get_id()])->delete()->execute();
        return (App::get_ci()->s->get_affected_rows() > 0);
    }

    /**
     * @param int $user_id
     * @param int $boosterpack_id
     * @return int
     */
    public static function buy(int $user_id, int $boosterpack_id)
    {
        $likes = 0;
        App::get_ci()->s->start_trans();
        try {
            $booster = new self($boosterpack_id);

            $to = $booster->get_price() + $booster->get_bank();
            $likes = rand(1, $to);
            $bank = $booster->get_price() - $likes;

            User_model::changeMoney($user_id, $booster->get_price(), Account_model::TYPE_WITHDRAWAL);

            $user = new User_model($user_id);
            $user->set_likes($likes);

            Account_model::create([
                'amount' => $likes,
                'user_id' => $user_id,
                'type' => Account_model::TYPE_DEPOSIT,
                'entity' => Account_model::ENTITY_LIKE
            ]);
            $booster->set_bank($bank);

        } catch (Exception $e) {
            App::get_ci()->s->rollback();
        }

        App::get_ci()->s->commit();

        return $likes;
    }

}
