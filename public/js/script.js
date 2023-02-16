function filterTable(tableId, filterString) {
  filterString = filterString.toLowerCase();
  let table = document.getElementById(tableId);
  let tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    let tags = tr[i].getAttribute("data-tags")?.split(",");
    if (tags) {
      let found = false;
      for (j = 0; j < tags.length; j++) {
        if (tags[j].toLowerCase().indexOf(filterString) > -1) {
          found = true;
          break;
        }
      }
      if (found) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}
