<?php


class UserRepository
{
    private array $users=[
        1 => [
            'id' => 1,
            'email' => 'jamshid@example.com',
            'password' => '$2y$10$ABC...',
            'name' => 'Jamshid',
            'role' => 'developer'
        ],
        2 => [
            'id' => 2,
            'email' => 'admin@example.com',
            'password' => '$2y$10$DEF...',
            'name' => 'Admin User',
            'role' => 'admin'
        ]

    ];

    public function __construct()
    {
        $this->users[1]['password'] = password_hash('secret123', PASSWORD_DEFAULT);
        $this->users[2]['password'] = password_hash('admin123', PASSWORD_DEFAULT);
    }

    public function findByEmail($email)
    {
        foreach ($this->users as $user) {
            if ($user['email'] == $email) {
                return $user;
            }
        }

        return null;
    }

    public function findById($id)
    {
        return $this->users[$id]??null;
    }
}