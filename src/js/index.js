const fillEmailAddresses = () => {
  [...document.querySelectorAll("[data-mailto]")].forEach(container => {
    const emailAddress = container.getAttribute("data-mailto");
    if (!emailAddress) {
      return;
    }

    container.setAttribute("href", `mailto:${emailAddress}@colby.edu`);
    container.innerHTML = `${emailAddress}@colby.edu`;
  });
};

window.addEventListener("load", fillEmailAddresses);
