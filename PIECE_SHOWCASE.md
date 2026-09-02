# Piece Showcase V4

## Cambios

- Encuadre horizontal independiente: 0 / 25 / 50 / 75 / 100%.
- Encuadre vertical independiente: 0 / 25 / 50 / 75 / 100%.
- El encuadre se aplica mediante `object-position`, sin modificar el archivo original.
- Nueva numeracion editorial visual para cada pieza.
- `Numero editorial` permite sobrescribir el numero (`01`, `02`, etc.).
- Si el eyebrow ya tiene formato `01 — HOME`, el componente separa automaticamente `01` y `HOME`.
- Si no se indica numero, se obtiene de la posicion de la pieza en la coleccion.
- Se puede ocultar el numero con `Mostrar numero editorial`.

## Recomendacion para Real Madrid — 125 Years

- Home: imagen derecha; X 50 o 25; Y 25 o 50 segun el recorte.
- Away: imagen izquierda; X 50; Y 50.
- Third: imagen derecha.
- Goalkeeper: imagen izquierda.
