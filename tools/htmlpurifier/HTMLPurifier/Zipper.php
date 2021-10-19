<?php

/**
 * A zipper is a purely-functional data structure which contains
 * a focus that can be efficiently manipulated.  It is known as
 * a "one-hole context".  This mutable variant implements a zipper
 * for a list as a pair of two arrays, laid out as follows:
 *
 *      Base list: 1 2 3 4 [ ] 6 7 8 9
 *      Front list: 1 2 3 4
 *      Back list: 9 8 7 6
 *
 * User is expected to keep track of the "current element" and properly
 * fill it back in as necessary.  (ToDo: Maybe it's more user friendly
 * to implicitly track the current element?)
 *
 * Nota bene: the current class gets confused if you try to store NULLs
 * in the list.
 */

class HTMLPurifier_Zipper
{
    public $front, $back;

    public function __construct($front, $back) {
        $this->front = $front;
        $this->back = $back;
    }

    /**
     * Creates a zipper from an array, with a hole in the
     * 0-index position.
     * @param Array to zipper-ify.
     * @return Tuple of zipper and element of first position.
     */
    static public function fromArray($array) {
        $z = new self(array(), array_reverse($array));
        $t = $z->delete(); // delete the "dummy hole"
        return array($z, $t);
    }

    /**
     * Convert zipper back into a normal array, optionally filling in
     * the hole with a value. (Usually you should supply a $t, unless you
     * are at the end of the array.)
     */
    public function toArray($t = NULL) {
        $a = $this->front;
        if ($t !== NULL) $a[] = $t;
        for ($i = count($this->back)-1; $i >= 0; $i--) {
            $a[] = $this->back[$i];
        }
        return $a;
    }

    /**
     * Move hole to the next element.
     * @param $t Element to fill hole with
     * @return Original contents of new hole.
     */
    public function next($t) {
        if ($t !== NULL) array_push($this->front, $t);
        return empty($this->back) ? NULL : array_pop($this->back);
    }

    /**
     * Iterated hole advancement.
     * @param $t Element to fill hole with
     * @param $i How many forward to advance hole
     * @return Original contents of new hole, i away
     */
    public function advance($t, $n) {
        for ($i = 0; $i < $n; $i++) {
            $t = $this->next($t);
        }
        return $t;
    }

    /**
     * Move hole to the previous element
     * @param $t Element to fill hole with
     * @return Original contents of new hole.
     */
    public function prev($t) {
        if ($t !== NULL) array_push($this->back, $t);
        return empty($this->front) ? NULL : array_pop($this->front);
    }

    /**
     * Delete contents of current hole, shifting hole to
     * next element.
     * @return Original contents of new hole.
     */
    public function delete() {
        return empty($this->back) ? NULL : array_pop($this->back);
    }

    /**
     * Returns true if we are at the end of the list.
     * @return bool
     */
    public function done() {
        return empty($this->back);
    }

    /**
     * Insert element before hole.
     * @param Element to insert
     */
    public function insertBefore($t) {
        if ($t !== NULL) array_push($this->front, $t);
    }

    /**
     * Insert element after hole.
     * @param Element to insert
     */
    public function insertAfter($t) {
        if ($t !== NULL) array_push($this->back, $t);
    }

    /**
     * Splice in multiple elements at hole.  Functional specification
     * in terms of array_splice:
     *
     *      $arr1 = $arr;
     *      $old1 = array_splice($arr1, $i, $delete, $replacement);
     *
     *      list($z, $t) = HTMLPurifier_Zipper::fromArray($arr);
     *      $t = $z->advance($t, $i);
     *      list($old2, $t) = $z->splice($t, $delete, $replacement);
     *      $arr2 = $z->toArray($t);
     *
     *      assert($old1 === $old2);
     *      assert($arr1 === $arr2);
     *
     * NB: the absolute index location after this operation is
     * *unchanged!*
     *
     * @param Current contents of hole.
     */
    public function splice($t, $delete, $replacement) {
        // delete
        $old = array();
        $r = $t;
        for ($i = $delete; $i > 0; $i--) {
            $old[] = $r;
            $r = $this->delete();
        }
        // insert
        for ($i = count($replacement)-1; $i >= 0; $i--) {
            $this->insertAfter($r);
            $r = $replacement[$i];
        }
        return array($old, $r);
    }
}
