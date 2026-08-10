/**
 * Desktop nav overflow — moves overflowing menu items into "Lainnya" dropdown.
 *
 * @package Temadesa
 */
(function () {
  "use strict";

  var container = document.querySelector(".desa-nav-desktop");
  if (!container) {
    return;
  }

  var nav = container.querySelector(".desa-nav");
  var more = nav ? nav.querySelector(".desa-more-menu") : null;
  var moreMenu = more ? more.querySelector(".dropdown-menu") : null;
  var actions = container.querySelector(".desa-nav-actions");

  if (!nav || !more || !moreMenu) {
    return;
  }

  var GAP = 28; // spacing allowance around nav + actions.

  /**
   * Return all top-level items except the "Lainnya" menu.
   */
  function topItems() {
    return Array.prototype.slice.call(nav.children).filter(function (li) {
      return li !== more;
    });
  }

  /**
   * Restore all items back to the main nav.
   */
  function restoreAll() {
    while (moreMenu.firstChild) {
      nav.insertBefore(moreMenu.firstChild, more);
    }
    more.classList.add("d-none");

    topItems().forEach(function (li) {
      li.classList.remove("desa-more-item");
      var a = li.querySelector(":scope > a");
      if (a) {
        a.classList.remove("dropdown-item");
        a.classList.add("nav-link");
      }
    });
  }

  /**
   * Move a single item into the "Lainnya" dropdown.
   */
  function moveToMore(li) {
    li.classList.add("desa-more-item");
    var a = li.querySelector(":scope > a");
    if (a) {
      a.classList.remove("nav-link");
      a.classList.add("dropdown-item");
    }
    moreMenu.insertBefore(li, moreMenu.firstChild);
  }

  /**
   * Measure width of items as laid out — from first item's left edge to
   * last item's right edge. Uses rects so flex gap is included.
   */
  function measure(items) {
    if (!items.length) {
      return 0;
    }
    var f = items[0].getBoundingClientRect();
    var l = items[items.length - 1].getBoundingClientRect();
    return l.right - f.left;
  }

  /**
   * Main handler: move items out until everything fits.
   */
  function handleOverflow() {
    // Desktop breakpoint — matches navbar-expand-md (offcanvas below).
    if (window.innerWidth < 768) {
      restoreAll();
      return;
    }

    restoreAll();

    // Measure against the .container wrapper, not the nav flex item itself:
    // the item grows with its overflowing content, so clientWidth is circular.
    var wrapper = container.parentElement;
    var brand = wrapper ? wrapper.querySelector(".desa-brand") : null;
    if (!wrapper || !brand) {
      return;
    }
    var wRect = wrapper.getBoundingClientRect();
    var bRect = brand.getBoundingClientRect();
    var available =
      wRect.right - bRect.right - GAP - (actions ? actions.offsetWidth : 0);

    var items = topItems();
    var total = measure(items);

    // Measure "Lainnya" while visible (restoreAll hid it).
    more.classList.remove("d-none");
    var moreWidth = more.offsetWidth + 12; // + flex gap before it.

    if (total <= available) {
      more.classList.add("d-none");
      return; // all fits, more stays hidden.
    }

    // Move from last to first until remaining items fit.
    more.classList.remove("d-none");
    for (var i = items.length - 1; i >= 0; i--) {
      moveToMore(items[i]);

      var remaining = topItems();
      if (measure(remaining) + moreWidth <= available) {
        break;
      }
    }

    if (moreMenu.children.length === 0) {
      more.classList.add("d-none");
    }
  }

  function debounce(fn, wait) {
    var t;
    return function () {
      clearTimeout(t);
      t = setTimeout(fn, wait);
    };
  }

  // Re-measure once fonts are loaded for accurate widths.
  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(handleOverflow);
  } else {
    window.addEventListener("load", handleOverflow);
  }

  window.addEventListener("resize", debounce(handleOverflow, 150));
})();
