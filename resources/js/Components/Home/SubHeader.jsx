export default function SubHeader({siteConfig}) {
  return(
    <div id="homepage-subheader" style={{ backgroundImage: `url(${siteConfig.subheader_image_path})`, height: "150px"}}></div>
  );
}