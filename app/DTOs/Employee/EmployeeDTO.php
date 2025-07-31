<?php

namespace App\DTOs\Employee;

class EmployeeDTO
{
    public string $name;
    public string $email;
    public string $designation;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->designation = $data['designation'];
    }
}
