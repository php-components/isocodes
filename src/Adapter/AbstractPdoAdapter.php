<?php
/**
 * ISO Codes
 *
 * ISO Codes abstract PDO adapter.
 *
* Copyright (c) 2016 Juan Pedro Gonzalez Gutierrez
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */
namespace ISOCodes\Adapter;

abstract class AbstractPdoAdapter extends AbstractAdapter
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * Constructor.
     * 
     * @param Pdo $pdo The PDO object to use or null to use the internal sqlite database.
     * @throws Exception\RuntimeException
     */
    public function __construct(\PDO $pdo = null)
    {
        if ($pdo instanceof \PDO) {
            $this->pdo = $pdo;
        } else {
            if (file_exists(dirname(dirname(__DIR__)) . '/data/sqlite/isocodes.sqlite')) {
                $this->pdo = new \PDO('sqlite:' . dirname(dirname(__DIR__)) . '/data/sqlite/isocodes.sqlite');
                $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } else {
                throw new Exception\RuntimeException('SQLite database not found.');
            }
        }
    }
}