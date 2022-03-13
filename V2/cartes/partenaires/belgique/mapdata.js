var simplemaps_countrymap_mapdata={
  main_settings: {
   //General settings
    width: "responsive", //'700' or 'responsive'
    background_color: "#FFFFFF",
    background_transparent: "yes",
    border_color: "#ffffff",
    
    //State defaults
    state_description: "",
    state_color: "#88A4BC",
    state_hover_color: "#3B729F",
    state_url: "",
    border_size: 1.5,
    all_states_inactive: "no",
    all_states_zoomable: "yes",
    
    //Location defaults
    location_description: "",
    location_url: "",
    location_color: "#FF0067",
    location_opacity: 1,
    location_hover_opacity: 1,
    location_size: 35,
    location_type: "marker",
    location_image_source: "frog.png",
    location_border_color: "#FFFFFF",
    location_border: 2,
    location_hover_border: 2.5,
    all_locations_inactive: "no",
    all_locations_hidden: "no",
    
    //Label defaults
    label_color: "#d5ddec",
    label_hover_color: "#d5ddec",
    label_size: 22,
    label_font: "Arial",
    hide_labels: "no",
    hide_eastern_labels: "no",
   
    //Zoom settings
    zoom: "yes",
    manual_zoom: "yes",
    back_image: "no",
    initial_back: "no",
    initial_zoom: "-1",
    initial_zoom_solo: "no",
    region_opacity: 1,
    region_hover_opacity: 0.6,
    zoom_out_incrementally: "yes",
    zoom_percentage: 0.99,
    zoom_time: 0.5,
    
    //Popup settings
    popup_color: "white",
    popup_opacity: 0.9,
    popup_shadow: 1,
    popup_corners: 5,
    popup_font: "12px/1.5 Verdana, Arial, Helvetica, sans-serif",
    popup_nocss: "no",
    
    //Advanced settings
    div: "map",
    auto_load: "yes",
    url_new_tab: "no",
    images_directory: "default",
    fade_time: 0.1,
    link_text: "Voir le site",
    popups: "on_click",
    state_image_url: "",
    state_image_position: "",
    location_image_url: ""
  },
  state_specific: {
    BEL2: {
      name: "Hainaut"
    },
    BEL3: {
      name: "Limburg"
    },
    BEL3474: {
      name: "Bruxelles"
    },
    BEL3475: {
      name: "Brabant Flamand"
    },
    BEL3476: {
      name: "Namur"
    },
    BEL3477: {
      name: "Luxembourg"
    },
    BEL3478: {
      name: "Flandre Orientale"
    },
    BEL3479: {
      name: "Flandre Occidentale"
    },
    BEL3480: {
      name: "Anwers"
    },
    BEL3481: {
      name: "Liège"
    },
    BEL3482: {
      name: "Brabant Wallon"
    }
  },
  locations: {},
  labels: {},
  legend: {
    entries: []
  },
  regions: {}
};
simplemaps_countrymap_mapdata.locations = locations;