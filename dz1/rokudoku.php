<?php






?>

<h1> <b>Rokudoku!</b> </h1>
<form class="" action="index.html" method="get">
  <label for="name">Unesi svoje ime:</label>
  <input type="text" id="name" name="name">
  <input type="submit" value="Započni igru!"><br>
</form>

<form class="" action="index.html" method="get">
  <input type="radio" id="option1" >Unesi broj <input type="text" id="number"> u redak
    <select id="redak" >
      <option value="r1">1</option>
      <option value="r2">2</option>
      <option value="r3">3</option>
      <option value="r4">4</option>
      <option value="r5">5</option>
      <option value="r6">6</option>
    </select> i stupac
    <select id="stupac" >
      <option value="s1">1</option>
      <option value="s2">2</option>
      <option value="s3">3</option>
      <option value="s4">4</option>
      <option value="s5">5</option>
      <option value="s6">6</option>
    </select> <br>
  <input type="radio" id="option2">Obriši broj iz retka
    <select id="redak" >
      <option value="r1">1</option>
      <option value="r2">2</option>
      <option value="r3">3</option>
      <option value="r4">4</option>
      <option value="r5">5</option>
      <option value="r6">6</option>
    </select> i stupca
    <select id="stupac" >
      <option value="s1">1</option>
      <option value="s2">2</option>
      <option value="s3">3</option>
      <option value="s4">4</option>
      <option value="s5">5</option>
      <option value="s6">6</option>
    </select> <br>
  <input type="radio" id="option3">Želim sve ispočetka<br>
  <input type="submit" value="Izvrši akciju!"><br>
</form>
