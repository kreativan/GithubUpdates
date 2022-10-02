// console.log(ProcessWire.config.GithubUpdates);

/**
 * Main update check function 
 * fetch vendor.json file data with download_url
 * then run checkForUpdates() to get the data
 */
function runUpdateCheck() {

  event.preventDefault();

  let icon = document.querySelector("#check-for-update .fa-refresh");
  if(icon) icon.classList.add("fa-spin");

  let status_msg = document.querySelector("#ivm-status-message");
  let check_msg = document.querySelector("#ivm-checking");

  if (status_msg) status_msg.classList.add("uk-hidden");
  if (check_msg) check_msg.classList.remove("uk-hidden");

  const config = ProcessWire.config.GithubUpdates;
  const github_repo_url = `https://api.github.com/repos/${config.user}/${config.repo}`;
  const github_file_url = `https://api.github.com/repos/${config.user}/${config.repo}/contents/${config.json_folder}`;

  fetch(github_file_url, {
      cache: "no-store",
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `token ${config.token}`,
      }
    })
    .then(response => {
      if (!response.ok) updateError(`${response.status} ${response.statusText}`);
      return response.json();
    })
    .then(data => {

      if(config.debug) console.log(data);
      if(data.message) {
        updateError(data.message);
      } else {
        checkForUpdates(data.download_url);
      }

      if(icon) icon.classList.remove("fa-spin");
      if(status_msg) status_msg.classList.remove("uk-hidden");
      if(check_msg) check_msg.classList.add("uk-hidden");

    })
    .catch((error) => {
      console.error('Error:', error);
    });

}

/**
 * Get the vendor.json data
 * and compare versions
 * @param {*} download_url 
 */
function checkForUpdates(download_url) {

  const config = ProcessWire.config.GithubUpdates;

  fetch(download_url)
    .then(response => response.json())
    .then(data => {

      let next_version = data.version;
      let pending_version = config.pending_version;
      let current_version = config.current_version;
      let needUpdate = compareVersion(current_version, next_version);

      //console.log(config);
      //console.log(data);

      //console.log(next_version);
      //console.log(pending_version);
      //console.log(current_version);
      //console.log(needUpdate);

      if (next_version === pending_version) {

        UIkit.modal.confirm(`<div class='uk-text-center'><h2 class='uk-margin-small'>Update Pending</h2>Latest <b>v${config.pending_version}</b> update is pending, and ready to install...</div>`)
          .then(function () {
            // dome something
          }, function () {
            console.log('Rejected.')
          });

      } else if (needUpdate) {

        UIkit.modal.confirm(`<div class='uk-text-center'><h2 class='uk-margin-small'>Update Available</h2><b>v${data.version}</b> update available, do you want to download it?</div>`)
          .then(function () {
            window.location = "./?download_update=1";
            document.querySelector("#ivm-status-message").classList.add("uk-hidden");
            document.querySelector("#ivm-downloading").classList.remove("uk-hidden");
          }, function () {
            console.log('Rejected.')
          });

      } else {

        UIkit.modal.alert('<h2 class="uk-text-center uk-margin-remove">All Good!</h2><div class="uk-text-center">Project is up to date.</div>');

      }

    })
    .catch((error) => {
      console.error('Error:', error);
    });

}

//
//  Display fetch error
//
function updateError(error) {
  UIkit.modal.alert(`<h3 class='uk-margin-remove uk-text-center uk-text-danger'><i class='fa fa-exclamation-circle'></i> ${error}</h3>`)
}

/**
 *  Trigger update install
 *
 */
function installUpdates() {
  event.preventDefault();
	let cog = event.target.querySelector(".fa-cog");
	if (cog) cog.classList.add("fa-spin"); 
  UIkit.modal.confirm(`<h2 class='uk-margin-small uk-text-center'>Install Updates?</h2>`)
    .then(function () {
      let status_msg = document.querySelector("#wk-status-message");
      let check_msg = document.querySelector("#wk-checking");
      let download_msg = document.querySelector("#wk-downloading");
      let install_msg = document.querySelector("#wk-installing");
      if (status_msg) status_msg.classList.add("uk-hidden");
      if (check_msg) check_msg.classList.add("uk-hidden");
      if (download_msg) download_msg.classList.add("uk-hidden");
      if (install_msg) install_msg.classList.remove("uk-hidden");
      window.location = "./?install_updates=1";
    }, function () {
      console.log('Rejected.')
			if (cog) cog.classList.remove("fa-spin"); 
    });
}


/**
 * Compare two versions
 * @param {*} version1 
 * @param {*} version2 
 * @returns 
 */
function compareVersion(version1,version2){
  var result=false;
  if(typeof version1 !=='object'){ version1=version1.toString().split('.'); }
  if(typeof version2 !=='object'){ version2=version2.toString().split('.'); }
  for(var i=0;i<(Math.max(version1.length,version2.length));i++){
    if(version1[i]==undefined){ version1[i]=0; }
    if(version2[i]==undefined){ version2[i]=0; }
    if(Number(version1[i])<Number(version2[i])){
      result=true;
      break;
    }
    if(version1[i]!=version2[i]){
      break;
    }
  }
  return(result);
}
