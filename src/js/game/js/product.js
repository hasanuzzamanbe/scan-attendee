function Product(x, type, speed, width) {
    this.selectedImage = productImages[Math.floor(Math.random() * productImages.length)]
    this.origin = x;
    this.position = createVector(0, 0);
    this.serpentine = random(3) + 4;
    this.squashed = false;
    this.radius = 60; // size of bug
    this.speed = speed;
    this.onLeaving = false;
    this.width = width;
    this.type = 'product'

    this.frames = (cartSpriteData.frames)
    this.productAnimation = null;
    if(cartAnimation == null){
        cartAnimation = [];
        for (let i = 0; i < this.frames.length; i++) {
            let pos = this.frames[i].frame;
            let img = cartSpriteSheet.get(pos.x, pos.y, pos.w, pos.h);
            cartAnimation.push(img);
        }
    }
}


Product.prototype.draw = function() {

    if(this.squashed){
        if(this.productAnimation == null){
            this.productAnimation = new Sprite(
                cartAnimation,
                this.position.x - (this.radius/2),
                this.position.y - (this.radius /2),
                0.4,
                this.radius);
        }
        this.productAnimation.show()
        this.productAnimation.animate()
        this.productAnimation.updateCoordinate(
            this.position.x - (this.radius/2),
            this.position.y - (this.radius /2),
        )
    }else{
        image(this.selectedImage,
            this.position.x - (this.radius/2),
            this.position.y - (this.radius /2),
            50,
            50
        )
    }
};


Product.prototype.update = function(isBombExploded) {
    this.position.y += this.speed;
    this.position.x = cos(this.position.y * (0.005 * this.serpentine) + this.serpentine * 10) * (this.width / this.serpentine) + this.origin;
}

Product.prototype.squashedBy = function(x, y) {
    var d = dist(x, y, this.position.x, this.position.y);
    return (d < this.radius);
};

Product.prototype.isBugOutOfScreen = function (){
    return this.position.y+this.radius > canvasHeight
}