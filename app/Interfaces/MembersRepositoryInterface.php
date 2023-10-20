<?php

interface MembersRepositoryInterface {
    public function findByUuid(string $uuid);
    public function findByEmail(string $email);
    public function all();
}
