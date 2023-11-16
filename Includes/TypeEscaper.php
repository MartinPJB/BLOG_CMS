<?php

namespace Includes;

/**
 * Permet d'escape les données passées en argument en fonction de leur type.
 */
class TypeEscaper
{
  /**
   * Permet d'escape les chaines de caractères passées en argument.
   *
   * @param string $data La chaine
   * @return string La chaine escaped
   */
  public static function escapeString(string $data): string
  {
    $data = htmlspecialchars($data);
    $data = strip_tags($data);

    return $data;
  }

  /**
   * Permet d'escape les nombres entiers passés en argument.
   *
   * @param integer $data Le nombre
   * @return integer Le nombre escaped
   */
  public static function escapeInt(int $data): int
  {
    $data = intval($data);
    return $data;
  }
}

