<?php
// app/DTOs/RegisterUserDTO.php
namespace App\DTOs\Registration;

class RegisterUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $role // role name like "admin" or "user"
    )
    {
    }
}
