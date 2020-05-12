<?php

class Account_model extends CI_Emerald_Model
{
    const CLASS_TABLE = 'account';

    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAWAL = 'withdrawal';
    const ENTITY_MONEY = 'money';
    const ENTITY_LIKE = 'like';

    /** @var int */
    protected $user_id;
    /** @var float */
    protected $amount;
    /** @var string */
    protected $type;

    /** @var string */
    protected $time_created;
    /** @var string */
    protected $time_updated;

    protected $user;

    /**
     * Account_model constructor.
     * @param null $id
     * @throws Exception
     */
    public function __construct($id = NULL)
    {
        parent::__construct();
        App::get_ci()->load->model('User_model');
        $this->set_id($id);
    }

    /**
     * @return int
     */
    public function get_user_id(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     *
     * @return bool
     */
    public function set_user_id(int $user_id)
    {
        $this->user_id = $user_id;
        return $this->save('user_id', $user_id);
    }

    /**
     * @return float
     */
    public function get_amount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return bool
     */
    public function set_amount(float $amount)
    {
        $this->amount = $amount;
        return $this->save('amount', $amount);
    }


    /**
     * @return string
     */
    public function get_type(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function set_type(string $type)
    {
        $this->type = $type;
        return $this->save('type', $type);
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
    public function set_time_updated(int $time_updated)
    {
        $this->time_updated = $time_updated;
        return $this->save('time_updated', $time_updated);
    }

    /**
     * @return User_model
     */
    public function get_user():User_model
    {
        if (empty($this->user))
        {
            try {
                $this->user = new User_model($this->get_user_id());
            } catch (Exception $exception)
            {
                $this->user = new User_model();
            }
        }
        return $this->user;
    }

    public function reload(bool $for_update = FALSE)
    {
        parent::reload($for_update);

        return $this;
    }

    /**
     * @param array $data
     * @return Account_model
     * @throws Exception
     */
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
}
