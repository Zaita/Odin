export default function SubHeader({siteConfig}) {
  return(
    <div id="homepage-subheader" style={{ backgroundImage: `url(${siteConfig.subHeaderImagePath})`, height: "150px"}}></div>
  );
}